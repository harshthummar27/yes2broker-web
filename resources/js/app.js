import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
    initPropertyCarousel();
    initFeaturedCarousel();
    initLocalityTabs();
    initAccordion();
    initLoadMore();
    initEmiCalculator();
    initConsultationModal();
    initPropertyGallery();
    initPropertyMobileSlider();
    initPropertyFaq();
    initPropertyInquiryModal();
    initHomeLoanBankModal();
});

function initMobileMenu() {
    const toggle = document.getElementById('mobile-menu-toggle');
    const menu = document.getElementById('mobile-menu');
    const iconOpen = document.getElementById('menu-icon-open');
    const iconClose = document.getElementById('menu-icon-close');

    if (!toggle || !menu) return;

    toggle.addEventListener('click', () => {
        const isOpen = !menu.classList.contains('hidden');
        menu.classList.toggle('hidden');
        iconOpen?.classList.toggle('hidden', !isOpen);
        iconClose?.classList.toggle('hidden', isOpen);
    });
}

function initPropertyCarousel() {
    const carousel = document.getElementById('property-carousel');
    const prev = document.getElementById('carousel-prev');
    const next = document.getElementById('carousel-next');

    if (!carousel || !prev || !next) return;

    const scrollAmount = 340;

    prev.addEventListener('click', () => {
        carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    });

    next.addEventListener('click', () => {
        carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    });
}

function initFeaturedCarousel() {
    const slides = document.querySelectorAll('[data-featured-slide]');
    const dots = document.querySelectorAll('[data-featured-dot]');
    let current = 0;

    if (slides.length === 0) return;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle('hidden', i !== index);
        });
        dots.forEach((dot, i) => {
            dot.classList.toggle('bg-white', i === index);
            dot.classList.toggle('bg-white/40', i !== index);
        });
        current = index;
    }

    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => showSlide(i));
    });

    setInterval(() => {
        showSlide((current + 1) % slides.length);
    }, 5000);
}

function initAccordion() {
    const triggers = document.querySelectorAll('[data-accordion-trigger]');

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const index = trigger.dataset.accordionTrigger;
            const panel = document.querySelector(`[data-accordion-panel="${index}"]`);
            const icon = document.querySelector(`[data-accordion-icon="${index}"]`);

            if (!panel) return;

            const isOpen = !panel.classList.contains('hidden');

            document.querySelectorAll('[data-accordion-panel]').forEach((p) => p.classList.add('hidden'));
            document.querySelectorAll('[data-accordion-trigger]').forEach((t) => t.classList.remove('bg-white/10'));
            document.querySelectorAll('[data-accordion-icon]').forEach((i) => i.classList.remove('rotate-180'));

            if (!isOpen) {
                panel.classList.remove('hidden');
                trigger.classList.add('bg-white/10');
                icon?.classList.add('rotate-180');
            }
        });
    });
}

function initLoadMore() {
    const button = document.getElementById('load-more-properties');
    const grid = document.getElementById('property-grid');

    if (!button || !grid) return;

    button.addEventListener('click', async () => {
        const nextPage = parseInt(button.dataset.page, 10) + 1;
        const params = new URLSearchParams(window.location.search);
        params.set('page', String(nextPage));

        const label = button.querySelector('.load-more-text');
        button.disabled = true;
        if (label) label.textContent = 'Loading...';

        try {
            const response = await fetch(`${button.dataset.url}?${params.toString()}`, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) throw new Error('Failed to load properties');

            const data = await response.json();
            grid.insertAdjacentHTML('beforeend', data.html);
            button.dataset.page = String(data.page);

            if (!data.hasMore) {
                button.closest('.load-more-wrap')?.remove();
                return;
            }

            if (label) label.textContent = 'Load More Properties';
            button.disabled = false;
        } catch {
            if (label) label.textContent = 'Load More Properties';
            button.disabled = false;
        }
    });
}

function initConsultationModal() {
    const modal = document.getElementById('consultation-modal');
    const backdrop = document.getElementById('consultation-modal-backdrop');
    const closeBtn = document.getElementById('consultation-modal-close');
    const triggers = document.querySelectorAll('[data-open-consultation]');

    if (!modal) return;

    function openModal() {
        modal.classList.add('is-open');
        modal.classList.remove('hidden');
        document.body.classList.add('consultation-modal-open');
        closeBtn?.focus();
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.classList.add('hidden');
        document.body.classList.remove('consultation-modal-open');
    }

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            document.getElementById('mobile-menu')?.classList.add('hidden');
            document.getElementById('menu-icon-open')?.classList.remove('hidden');
            document.getElementById('menu-icon-close')?.classList.add('hidden');
            openModal();
        });
    });

    closeBtn?.addEventListener('click', closeModal);
    backdrop?.addEventListener('click', closeModal);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });
}

function initEmiCalculator() {
    const root = document.getElementById('emi-calculator');
    if (!root) return;

    const loanSlider = document.getElementById('emi-loan-amount');
    const tenureSlider = document.getElementById('emi-tenure');
    const interestSlider = document.getElementById('emi-interest');
    const canvas = document.getElementById('emi-chart');

    if (!loanSlider || !tenureSlider || !interestSlider || !canvas) return;

    const fmt = new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    });

    const loanLabel = document.getElementById('emi-loan-label');
    const tenureLabel = document.getElementById('emi-tenure-label');
    const rateLabel = document.getElementById('emi-rate-label');
    const emiAmount = document.getElementById('emi-amount');
    const loanOut = document.getElementById('emi-loan-out');
    const interestOut = document.getElementById('emi-interest-out');
    const totalOut = document.getElementById('emi-total-out');

    function drawChart(principal, interest) {
        const ctx = canvas.getContext('2d');
        const size = canvas.width;
        const center = size / 2;
        const radius = size / 2 - 8;
        const inner = radius * 0.65;
        const total = principal + interest;

        ctx.clearRect(0, 0, size, size);

        if (total <= 0) return;

        let start = -Math.PI / 2;
        const slices = [
            { value: principal, color: '#001b73' },
            { value: interest, color: '#d8e3ff' },
        ];

        slices.forEach((slice) => {
            const angle = (slice.value / total) * Math.PI * 2;
            ctx.beginPath();
            ctx.arc(center, center, radius, start, start + angle);
            ctx.arc(center, center, inner, start + angle, start, true);
            ctx.closePath();
            ctx.fillStyle = slice.color;
            ctx.fill();
            start += angle;
        });
    }

    function calculate() {
        const principal = parseFloat(loanSlider.value);
        const years = parseInt(tenureSlider.value, 10);
        const rate = parseFloat(interestSlider.value);
        const months = years * 12;
        const monthlyRate = rate / (12 * 100);

        let emi;
        if (monthlyRate === 0) {
            emi = principal / months;
        } else {
            const factor = Math.pow(1 + monthlyRate, months);
            emi = (principal * monthlyRate * factor) / (factor - 1);
        }

        const totalPayable = emi * months;
        const totalInterest = totalPayable - principal;

        loanLabel.textContent = fmt.format(principal);
        tenureLabel.textContent = `${years} Years`;
        rateLabel.textContent = `${rate.toFixed(1)}%`;
        emiAmount.textContent = fmt.format(emi);
        loanOut.textContent = fmt.format(principal);
        interestOut.textContent = fmt.format(totalInterest);
        totalOut.textContent = fmt.format(totalPayable);

        drawChart(principal, totalInterest);
    }

    ['input', 'change'].forEach((event) => {
        loanSlider.addEventListener(event, calculate);
        tenureSlider.addEventListener(event, calculate);
        interestSlider.addEventListener(event, calculate);
    });

    calculate();
}

function initPropertyGallery() {
    const main = document.getElementById('property-gallery-main');
    const thumbs = document.querySelectorAll('[data-property-gallery-thumb]');

    if (!main || thumbs.length === 0) return;

    thumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => {
            const image = thumb.dataset.image;
            if (!image) return;

            main.src = image;
            main.alt = main.alt || '';

            thumbs.forEach((item) => {
                const isActive = item === thumb;
                item.classList.toggle('border-y2b-primary', isActive);
                item.classList.toggle('border-transparent', !isActive);
                item.classList.toggle('opacity-70', !isActive);
                item.classList.toggle('opacity-100', isActive);
            });
        });
    });
}

function initPropertyMobileSlider() {
    const track = document.querySelector('[data-property-mobile-track]');
    const dotsWrap = document.querySelector('[data-property-mobile-dots]');
    const dots = document.querySelectorAll('[data-property-mobile-dot]');

    if (!track || !dotsWrap || dots.length === 0) return;

    function setActiveDot(activeIndex) {
        dots.forEach((dot, index) => {
            dot.classList.toggle('bg-y2b-primary', index === activeIndex);
            dot.classList.toggle('bg-gray-300', index !== activeIndex);
        });
    }

    function getActiveIndex() {
        const width = track.clientWidth || 1;
        return Math.round(track.scrollLeft / width);
    }

    let raf = null;
    track.addEventListener('scroll', () => {
        if (raf) cancelAnimationFrame(raf);
        raf = requestAnimationFrame(() => setActiveDot(getActiveIndex()));
    }, { passive: true });

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            const index = parseInt(dot.dataset.propertyMobileDot || '0', 10);
            const left = index * track.clientWidth;
            track.scrollTo({ left, behavior: 'smooth' });
            setActiveDot(index);
        });
    });
}

function initPropertyFaq() {
    const root = document.getElementById('property-faq-accordion');
    if (!root) return;

    const triggers = root.querySelectorAll('[data-property-faq-trigger]');

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const index = trigger.dataset.propertyFaqTrigger;
            const panel = root.querySelector(`[data-property-faq-panel="${index}"]`);
            const icon = root.querySelector(`[data-property-faq-icon="${index}"]`);

            if (!panel) return;

            const isOpen = !panel.classList.contains('hidden');

            root.querySelectorAll('[data-property-faq-panel]').forEach((p) => p.classList.add('hidden'));
            root.querySelectorAll('[data-property-faq-trigger]').forEach((t) => t.classList.remove('bg-gray-50'));
            root.querySelectorAll('[data-property-faq-icon]').forEach((i) => i.classList.remove('rotate-180'));

            if (!isOpen) {
                panel.classList.remove('hidden');
                trigger.classList.add('bg-gray-50');
                icon?.classList.add('rotate-180');
            }
        });
    });
}

function initPropertyInquiryModal() {
    const modal = document.getElementById('property-inquiry-modal');
    const backdrop = document.getElementById('property-inquiry-modal-backdrop');
    const closeBtn = document.getElementById('property-inquiry-modal-close');
    const triggers = document.querySelectorAll('[data-open-property-inquiry]');

    if (!modal) return;

    function openModal() {
        modal.classList.add('is-open');
        modal.classList.remove('hidden');
        document.body.classList.add('consultation-modal-open');
        closeBtn?.focus();
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.classList.add('hidden');
        document.body.classList.remove('consultation-modal-open');
    }

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            openModal();
        });
    });

    closeBtn?.addEventListener('click', closeModal);
    backdrop?.addEventListener('click', closeModal);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });
}

function initHomeLoanBankModal() {
    const modal = document.getElementById('home-loan-bank-modal');
    const backdrop = document.getElementById('home-loan-bank-modal-backdrop');
    const closeBtn = document.getElementById('home-loan-bank-modal-close');
    const bankNameEl = document.getElementById('home-loan-bank-modal-name');
    const bankInput = document.getElementById('home-loan-bank-modal-input');
    const triggers = document.querySelectorAll('[data-open-home-loan-bank]');

    if (!modal) return;

    function openModal(bankName) {
        if (bankNameEl) bankNameEl.textContent = bankName;
        if (bankInput) bankInput.value = bankName;
        modal.classList.add('is-open');
        modal.classList.remove('hidden');
        document.body.classList.add('consultation-modal-open');
        closeBtn?.focus();
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.classList.add('hidden');
        document.body.classList.remove('consultation-modal-open');
    }

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            openModal(trigger.dataset.bankName || '');
        });
    });

    closeBtn?.addEventListener('click', closeModal);
    backdrop?.addEventListener('click', closeModal);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });
}

function initLocalityTabs() {
    const tabsContainer = document.getElementById('locality-tabs');
    const tabs = document.querySelectorAll('[data-locality-tab]');
    const panels = document.querySelectorAll('[data-locality-panel]');

    if (tabs.length === 0) return;

    function setActiveTab(activeTab) {
        const target = activeTab.dataset.localityTab;

        tabs.forEach((tab) => {
            const isActive = tab === activeTab;
            tab.classList.toggle('bg-y2b-primary', isActive);
            tab.classList.toggle('text-white', isActive);
            tab.classList.toggle('shadow-sm', isActive);
            tab.classList.toggle('bg-y2b-light', !isActive);
            tab.classList.toggle('text-y2b-primary', !isActive);
            tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        panels.forEach((panel) => {
            panel.classList.toggle('hidden', panel.dataset.localityPanel !== target);
        });

        if (tabsContainer) {
            const containerRect = tabsContainer.getBoundingClientRect();
            const tabRect = activeTab.getBoundingClientRect();
            const offset = tabRect.left - containerRect.left - (containerRect.width / 2) + (tabRect.width / 2);

            tabsContainer.scrollBy({ left: offset, behavior: 'smooth' });
        }
    }

    tabs.forEach((tab, index) => {
        tab.setAttribute('role', 'tab');
        tab.setAttribute('aria-selected', index === 0 ? 'true' : 'false');

        tab.addEventListener('click', () => setActiveTab(tab));
    });
}
