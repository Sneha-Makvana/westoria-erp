<style>
    input[type="text"],
    input[type="search"],
    input:not([type]),
    textarea {
        text-transform: uppercase;
    }
    input[type="password"],
    input[type="email"],
    input[type="number"],
    input[type="tel"],
    input[type="url"],
    input[type="date"],
    input[type="datetime-local"],
    input[type="time"],
    input[type="file"] {
        text-transform: none;
    }
</style>
<script>
    (function () {
        var skipTypes = {
            password: 1, email: 1, number: 1, tel: 1, url: 1, date: 1,
            'datetime-local': 1, time: 1, month: 1, week: 1, file: 1,
            hidden: 1, color: 1, range: 1, checkbox: 1, radio: 1,
            button: 1, submit: 1, reset: 1
        };

        function forceUpper(el) {
            if (!el || el.readOnly || el.disabled) {
                return;
            }
            var tag = el.tagName;
            if (tag !== 'INPUT' && tag !== 'TEXTAREA') {
                return;
            }
            var type = (el.getAttribute('type') || 'text').toLowerCase();
            if (skipTypes[type] || el.getAttribute('data-keep-case') === '1') {
                return;
            }
            var upper = String(el.value || '').toLocaleUpperCase('en-US');
            if (upper === el.value) {
                return;
            }
            var start = null;
            var end = null;
            try {
                start = el.selectionStart;
                end = el.selectionEnd;
            } catch (e) {}
            el.value = upper;
            if (start !== null && end !== null) {
                try { el.setSelectionRange(start, end); } catch (e) {}
            }
        }

        document.addEventListener('input', function (e) {
            forceUpper(e.target);
        }, true);
        document.addEventListener('focusin', function (e) {
            forceUpper(e.target);
        }, true);
        document.addEventListener('submit', function (e) {
            if (!e.target || !e.target.querySelectorAll) {
                return;
            }
            e.target.querySelectorAll('input, textarea').forEach(forceUpper);
        }, true);
    })();
</script>
