import 'sweetalert2/dist/sweetalert2.min.css';

document.querySelectorAll('[data-menu-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const expanded = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', String(!expanded));
        document.querySelector(button.getAttribute('aria-controls'))?.classList.toggle('is-open', !expanded);
    });
});

document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = button.parentElement.querySelector('input');
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        button.textContent = show ? 'Hide' : 'Show';
        button.setAttribute('aria-label', `${show ? 'Hide' : 'Show'} password`);
    });
});

const updateAccountFields = () => {
    const selected = document.querySelector('[data-account-type]:checked');
    const councilFields = document.querySelector('[data-council-fields]');
    if (!selected || !councilFields) return;

    const isCouncil = selected.value === 'council';
    councilFields.hidden = !isCouncil;
    councilFields.querySelectorAll('[data-required-council]').forEach((input) => {
        input.required = isCouncil;
        input.disabled = !isCouncil;
    });
};

document.querySelectorAll('[data-account-type]').forEach((input) => {
    input.addEventListener('change', updateAccountFields);
});
updateAccountFields();

const strengthInput = document.querySelector('[data-strength-input]');
if (strengthInput) {
    strengthInput.addEventListener('input', () => {
        const value = strengthInput.value;
        const score = [
            value.length >= 12,
            /[a-z]/.test(value) && /[A-Z]/.test(value),
            /\d/.test(value),
            /[^A-Za-z0-9]/.test(value),
        ].filter(Boolean).length;
        const meter = document.querySelector('[data-strength-meter]');
        const label = document.querySelector('[data-strength-label]');
        meter.style.width = `${score * 25}%`;
        meter.dataset.score = String(score);
        label.textContent = value.length === 0
            ? 'Use at least 12 characters.'
            : ['Add more variety to strengthen it.', 'A little stronger.', 'Looking good.', 'Strong password.'][Math.min(score, 4) - 1] ?? 'Add more variety to strengthen it.';
    });
}

document.querySelectorAll('[data-image-input]').forEach((input) => {
    input.addEventListener('change', () => {
        const preview = document.querySelector('[data-image-preview]');
        const file = input.files?.[0];
        if (!preview || !file) return;

        if (preview.dataset.objectUrl) URL.revokeObjectURL(preview.dataset.objectUrl);
        preview.dataset.objectUrl = URL.createObjectURL(file);
        preview.src = preview.dataset.objectUrl;
        preview.hidden = false;
    });
});

document.querySelectorAll('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const { default: Swal } = await import('sweetalert2');
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: form.dataset.confirm,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Continue',
            cancelButtonText: 'Keep it',
            confirmButtonColor: '#163b65',
            reverseButtons: true,
        });

        if (result.isConfirmed) HTMLFormElement.prototype.submit.call(form);
    });
});

document.querySelectorAll('[data-loading-form]').forEach((form) => {
    form.addEventListener('submit', () => {
        const button = form.querySelector('button[type="submit"]');
        if (button) {
            button.disabled = true;
            button.classList.add('is-loading');
        }
    });
});

const calendars = document.querySelectorAll('#community-calendar, #workspace-calendar');
if (calendars.length) {
    Promise.all([
        import('@fullcalendar/core'),
        import('@fullcalendar/daygrid'),
        import('@fullcalendar/timegrid'),
        import('@fullcalendar/list'),
        import('@fullcalendar/interaction'),
    ]).then(([{ Calendar }, { default: dayGridPlugin }, { default: timeGridPlugin }, { default: listPlugin }, { default: interactionPlugin }]) => {
        calendars.forEach((element) => {
            const calendar = new Calendar(element, {
                plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
                initialView: 'dayGridMonth',
                height: 'auto',
                events: JSON.parse(element.dataset.events || '[]'),
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
                },
                buttonText: { today: 'Today', month: 'Month', week: 'Week', day: 'Day', list: 'List' },
                dayMaxEvents: true,
                nowIndicator: true,
                eventDisplay: 'block',
            });
            calendar.render();
        });
    });
}

const statusChart = document.querySelector('#program-status-chart');
if (statusChart) {
    import('chart.js/auto').then(({ default: Chart }) => {
        const values = JSON.parse(statusChart.dataset.values || '{}');
        const labels = Object.keys(values);
        new Chart(statusChart, {
            type: 'doughnut',
            data: {
                labels: labels.map((label) => `${label.charAt(0).toUpperCase()}${label.slice(1)}`),
                datasets: [{
                    data: Object.values(values),
                    backgroundColor: ['#2563eb', '#0f9d91', '#d99b35', '#94a3b8'],
                    borderWidth: 0,
                    hoverOffset: 5,
                }],
            },
            options: {
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 18 } } },
            },
        });
    });
}
