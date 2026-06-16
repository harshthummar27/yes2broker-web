<?php

namespace App\Services;

use Illuminate\Support\Str;

class PropertyPageHtmlParser
{
    /**
     * @return array<string, mixed>
     */
    public function parse(string $html, string $slug, ?string $fallbackTitle = null): array
    {
        $iframes = $this->extractIframes($html);
        $gallery = $this->extractGalleryImages($html);
        $faqs = $this->extractFaqs($html);
        $overview = $this->extractOverview($html);
        $amenities = $this->extractAmenities($html);
        $description = $this->extractDescription($html);
        $price = $this->extractPrice($html);
        $bhk = $this->extractBhk($html);
        $title = $this->extractTitle($html) ?? $fallbackTitle ?? Str::title(str_replace('-', ' ', $slug));
        $location = $this->extractLocation($html, $faqs);

        return [
            'title' => $title,
            'location' => $location,
            'bhk' => $bhk,
            'price' => $price,
            'description' => $description,
            'overview' => $overview,
            'amenities' => $amenities,
            'faqs' => $faqs,
            'gallery' => $gallery,
            'map_embed_url' => $iframes['map'],
            'street_view_embed_url' => $iframes['street_view'],
            'brochure_url' => $this->extractBrochureUrl($html),
        ];
    }

    /**
     * @return array{map: ?string, street_view: ?string}
     */
    public function extractIframes(string $html): array
    {
        preg_match_all('/<iframe[^>]+src=["\']([^"\']+)["\']/i', $html, $matches);

        $map = null;
        $streetView = null;

        foreach ($matches[1] ?? [] as $src) {
            $src = html_entity_decode($src);

            if (str_contains($src, 'google.com/maps/embed?pb=')) {
                $streetView = $src;

                continue;
            }

            if (str_contains($src, 'maps.google.com/maps') || str_contains($src, 'google.com/maps')) {
                $map = $src;
            }
        }

        return ['map' => $map, 'street_view' => $streetView];
    }

    /**
     * @return list<string>
     */
    public function extractGalleryImages(string $html): array
    {
        preg_match_all(
            '#https://yes2broker\.in/wp-content/uploads/\d{4}/\d{2}/[^"\'\s<>]+\.(?:jpg|jpeg|png|webp|avif)(?:\?[^"\'\s<>]*)?#i',
            $html,
            $matches
        );

        $images = [];

        foreach ($matches[0] ?? [] as $url) {
            $url = html_entity_decode($url);

            if ($this->isGalleryImage($url)) {
                $images[] = $this->normalizeImageUrl($url);
            }
        }

        return array_values(array_unique($images));
    }

    private function isGalleryImage(string $url): bool
    {
        $lower = strtolower($url);

        if (str_contains($lower, 'logo') || str_contains($lower, 'cropped-y2b') || str_contains($lower, '465465')) {
            return false;
        }

        if (preg_match('/-\d+x\d+\./', $lower)) {
            return false;
        }

        return true;
    }

    private function normalizeImageUrl(string $url): string
    {
        return strtok($url, '?') ?: $url;
    }

    public function extractTitle(string $html): ?string
    {
        if (preg_match('/<title>([^<]+)<\/title>/i', $html, $match)) {
            return trim(str_replace(' – yes2broker', '', html_entity_decode($match[1])));
        }

        return null;
    }

    /**
     * @param  list<array{question: string, answer: string}>  $faqs
     */
    public function extractLocation(string $html, array $faqs): ?string
    {
        foreach ($faqs as $faq) {
            if (str_contains(strtolower($faq['question']), 'where is it located')) {
                return $faq['answer'];
            }
        }

        if (preg_match('/<li[^>]*>.*?<span[^>]*>.*?<\/span>\s*<span[^>]*>([^<]*(?:Ahmedabad|Gujarat|Road|Street)[^<]*\.?)<\/span>/is', $html, $match)) {
            return trim(html_entity_decode($match[1]));
        }

        return null;
    }

    public function extractDescription(string $html): ?string
    {
        if (! preg_match('/Descriptions<\/h4>.*?<p[^>]*>(.*?)<\/p>/is', $html, $match)) {
            return null;
        }

        return trim(html_entity_decode(strip_tags($match[1])));
    }

    /**
     * @return array<string, string>
     */
    public function extractOverview(string $html): array
    {
        if (! preg_match('/Overview<\/h4>(.*?)360 Degree View<\/h4>/is', $html, $section)) {
            return [];
        }

        $block = $section[1];

        $fields = [
            'project_area' => 'Project Area',
            'configurations' => 'Configurations & Sizes',
            'project_size' => 'Project Size',
            'launch_date' => 'Launch Date',
            'price_range' => 'Price Range',
            'possession' => 'Possession Date',
            'rera_id' => 'RERA ID',
        ];

        $overview = [];

        foreach ($fields as $key => $label) {
            $pattern = '/'.preg_quote($label, '/').'.*?<p[^>]*>(.*?)<\/p>/is';

            if (preg_match($pattern, $block, $match)) {
                $value = trim(html_entity_decode(strip_tags($match[1])));
                $value = preg_replace('/\s+/', ' ', $value) ?? $value;
                $value = str_replace([' 3 BHK', ' 4 BHK'], ["\n3 BHK", "\n4 BHK"], $value);
                $overview[$key] = $value;
            }
        }

        return $overview;
    }

    /**
     * @return list<string>
     */
    public function extractAmenities(string $html): array
    {
        if (! preg_match('/Additional Amenities<\/h4>(.*?)Frequently Asked Questions/is', $html, $section)) {
            return [];
        }

        preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $section[1], $items);

        $amenities = [];

        foreach ($items[1] ?? [] as $itemHtml) {
            $text = trim(html_entity_decode(strip_tags($itemHtml)));
            $text = preg_replace('/\s+/', ' ', $text) ?? $text;

            if ($text !== '') {
                $amenities[] = $text;
            }
        }

        if ($amenities !== []) {
            return array_values(array_unique($amenities));
        }

        preg_match_all('/elementor-icon-list-text">([^<]+)</', $section[1], $matches);

        return array_values(array_filter(array_unique(array_map(
            fn (string $item) => trim(html_entity_decode($item)),
            $matches[1] ?? []
        ))));
    }

    /**
     * @return list<array{question: string, answer: string}>
     */
    public function extractFaqs(string $html): array
    {
        if (! preg_match('/Frequently Asked Questions(.*?)Interested in This Property/is', $html, $section)) {
            return [];
        }

        preg_match_all('/<details[^>]*>.*?<summary[^>]*>(.*?)<\/summary>(.*?)<\/details>/is', $section[1], $items, PREG_SET_ORDER);

        $faqs = [];

        foreach ($items as $item) {
            $question = trim(html_entity_decode(strip_tags($item[1])));

            if (! preg_match('/<p[^>]*>(.*?)<\/p>/is', $item[2], $answerMatch)) {
                continue;
            }

            $answer = trim(html_entity_decode(strip_tags($answerMatch[1])));

            if ($question !== '' && $answer !== '') {
                $faqs[] = ['question' => $question, 'answer' => $answer];
            }
        }

        return $faqs;
    }

    public function extractPrice(string $html): ?string
    {
        if (preg_match('/₹[\d.]+\s*Lakhs?\s*[–-]\s*₹[\d.]+\s*Cr/i', $html, $match)) {
            return trim(html_entity_decode($match[0]));
        }

        if (preg_match('/₹[\d.]+\s*(?:Lakhs?|L\b|Cr)[^<]*/i', $html, $match)) {
            return trim(html_entity_decode($match[0]));
        }

        return null;
    }

    public function extractBhk(string $html): ?string
    {
        if (preg_match('/Features:.*?<span[^>]*>([^<]*(?:BHK|Shop)[^<]*)<\/span>/is', $html, $match)) {
            return trim(html_entity_decode($match[1]));
        }

        return null;
    }

    public function extractBrochureUrl(string $html): ?string
    {
        if (preg_match('/Download Brochure.*?href=["\']([^"\']+)["\']/is', $html, $match)) {
            $url = html_entity_decode($match[1]);

            if (str_contains($url, '.pdf')) {
                return $url;
            }
        }

        return null;
    }

    /**
     * @return array{location: ?string, bhk: ?string, area: ?string, possession: ?string, price: ?string}
     */
    public function parseListingExcerpt(string $excerpt): array
    {
        $data = [
            'location' => null,
            'bhk' => null,
            'area' => null,
            'possession' => null,
            'price' => null,
        ];

        if (preg_match('/class="location"[^>]*>([^<]+)</', $excerpt, $match)) {
            $data['location'] = trim(html_entity_decode(preg_replace('/^📍\s*/u', '', $match[1]) ?? $match[1]));
        }

        if (preg_match('/🛏\s*([^<]+)</u', $excerpt, $match)) {
            $data['bhk'] = trim(html_entity_decode($match[1]));
        }

        if (preg_match('/Project Area:\s*([^<]+)</', $excerpt, $match)) {
            $data['area'] = trim(html_entity_decode($match[1]));
        }

        if (preg_match('/Possession Date:\s*([^<]+)</', $excerpt, $match)) {
            $data['possession'] = trim(html_entity_decode($match[1]));
        }

        if (preg_match('/class="price"[^>]*>([^<]+)</', $excerpt, $match)) {
            $data['price'] = trim(html_entity_decode($match[1]));
        }

        return $data;
    }
}
