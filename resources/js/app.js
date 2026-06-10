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
            dot.classList.toggle('bg-y2b-accent', i === index);
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

function initLocalityTabs() {
    const tabs = document.querySelectorAll('[data-locality-tab]');
    const panels = document.querySelectorAll('[data-locality-panel]');

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.localityTab;

            tabs.forEach((t) => {
                t.classList.toggle('bg-y2b-primary', t.dataset.localityTab === target);
                t.classList.toggle('text-white', t.dataset.localityTab === target);
                t.classList.toggle('bg-y2b-light', t.dataset.localityTab !== target);
                t.classList.toggle('text-y2b-primary', t.dataset.localityTab !== target);
            });

            panels.forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.localityPanel !== target);
            });
        });
    });
}
