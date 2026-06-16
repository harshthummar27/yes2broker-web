<?php

namespace App\Services\Legacy;

use RuntimeException;

class WordPressPostsReader
{
    /**
     * @param  list<string>  $propertySlugs
     * @return array{
     *     pages: array<string, array{id: int, title: string, content: string}>,
     *     attachments: array<int, list<string>>
     * }
     */
    public function index(array $propertySlugs): array
    {
        $path = base_path('wp_posts.csv');

        if (! is_file($path)) {
            throw new RuntimeException('wp_posts.csv not found in project root.');
        }

        $slugLookup = array_fill_keys($propertySlugs, true);
        $pages = [];
        $attachments = [];

        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException('Unable to open wp_posts.csv');
        }

        $headers = fgetcsv($handle);

        if ($headers === false) {
            fclose($handle);

            throw new RuntimeException('wp_posts.csv is empty.');
        }

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 22) {
                continue;
            }

            $postType = (string) ($row[20] ?? '');
            $postStatus = (string) ($row[7] ?? '');
            $postName = (string) ($row[11] ?? '');

            if ($postType === 'page' && $postStatus === 'publish' && isset($slugLookup[$postName])) {
                $pages[$postName] = [
                    'id' => (int) ($row[0] ?? 0),
                    'title' => html_entity_decode((string) ($row[5] ?? '')),
                    'content' => (string) ($row[4] ?? ''),
                ];

                continue;
            }

            if ($postType === 'attachment' && str_starts_with((string) ($row[21] ?? ''), 'image/')) {
                $parentId = (int) ($row[17] ?? 0);
                $guid = (string) ($row[18] ?? '');

                if ($parentId > 0 && $guid !== '') {
                    $attachments[$parentId][] = $guid;
                }
            }
        }

        fclose($handle);

        return [
            'pages' => $pages,
            'attachments' => $attachments,
        ];
    }
}
