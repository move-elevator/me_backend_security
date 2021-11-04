(function() {

    const PasswordValidator = {};

    PasswordValidator.view = document.querySelector('#validator-view');
    PasswordValidator.passwordField = document.querySelector('#password-field');
    PasswordValidator.passwordRepeatField = document.querySelector('#password-repeat-field');

    if (PasswordValidator.view === null ||
        PasswordValidator.passwordField === null ||
        PasswordValidator.passwordRepeatField === null
    ) {
        return;
    }

    PasswordValidator.rules = [...document.querySelectorAll('.rule')];

    PasswordValidator.reset = function() {
        PasswordValidator.rules.forEach(function(rule) {
            const icons = [...rule.querySelectorAll('.icon')];

            icons[0].style.display = 'inline-block';
            icons[1].style.display = 'none';
        });
    };

    PasswordValidator.validatePasswordLength = function() {
        const rule = PasswordValidator.view.querySelector('.rule[data-validator-rule="passwordLength"]');

        if (rule === null) {
            return;
        }

        const passwordMinLength = rule.getAttribute('data-validator-rule-minimum');
        const icons = [...rule.querySelectorAll('.icon')];

        if (PasswordValidator.passwordField.value.length >= passwordMinLength) {
            icons[0].style.display = 'none';
            icons[1].style.display = 'inline-block';
        }
    };

    PasswordValidator.validateSpecialChar = function() {
        const rule = PasswordValidator.view.querySelector('.rule[data-validator-rule="specialChar"]');

        if (rule === null) {
            return;
        }

        const minimum = rule.getAttribute('data-validator-rule-minimum');
        const icons = [...rule.querySelectorAll('.icon')];
        const matches = PasswordValidator.passwordField.value.match(/[\\\[\]\/\-(){}@#?!$%&=*+~,.;:<>_]/g);

        if (matches !== null && matches.length >= minimum) {
            icons[0].style.display = 'none';
            icons[1].style.display = 'inline-block';
        }
    };

    PasswordValidator.validateDigit = function() {
        const rule = PasswordValidator.view.querySelector('.rule[data-validator-rule="digit"]');

        if (rule === null) {
            return;
        }

        const minimum = rule.getAttribute('data-validator-rule-minimum');
        const matches = PasswordValidator.passwordField.value.match(/[0-9]/g);
        const icons = [...rule.querySelectorAll('.icon')];

        if (matches !== null && matches.length >= minimum) {
            icons[0].style.display = 'none';
            icons[1].style.display = 'inline-block';
        }
    };

    PasswordValidator.validateCapitalChar = function() {
        const rule = PasswordValidator.view.querySelector('.rule[data-validator-rule="capitalChar"]');

        if (rule === null) {
            return;
        }

        const minimum = rule.getAttribute('data-validator-rule-minimum');
        const matches = PasswordValidator.passwordField.value.match(/[A-ZÄÖÜ]/g);
        const icons = [...rule.querySelectorAll('.icon')];

        if (matches !== null && matches.length >= minimum) {
            icons[0].style.display = 'none';
            icons[1].style.display = 'inline-block';
        }
    };

    PasswordValidator.validateLowercaseChar = function() {
        const rule = PasswordValidator.view.querySelector('.rule[data-validator-rule="lowercaseChar"]');

        if (rule === null) {
            return;
        }

        const minimum = rule.getAttribute('data-validator-rule-minimum');
        const matches = PasswordValidator.passwordField.value.match(/[a-zäöü]/g);
        const icons = [...rule.querySelectorAll('.icon')];

        if (matches !== null && matches.length >= minimum) {
            icons[0].style.display = 'none';
            icons[1].style.display = 'inline-block';
        }
    };

    PasswordValidator.validateDifferentPasswords = function() {
        const rule = PasswordValidator.view.querySelector('.rule[data-validator-rule="differentPasswords"]');

        if (rule === null) {
            return;
        }

        const icons = [...rule.querySelectorAll('.icon')];
        const passwordValue = PasswordValidator.passwordField.value.trim();
        const passwordRepeatValue = PasswordValidator.passwordRepeatField.value.trim();

        if (passwordValue !== '' &&
            passwordRepeatValue !== '' &&
            passwordValue === passwordRepeatValue
        ) {
            icons[0].style.display = 'none';
            icons[1].style.display = 'inline-block';
        }
    };

    PasswordValidator.init = function() {
        PasswordValidator.reset();
        PasswordValidator.view.style.display = 'none';
        PasswordValidator.view.classList.remove('hidden');

        PasswordValidator.passwordField.addEventListener('focus', function() {
            PasswordValidator.view.style.display = 'block';
        });

        PasswordValidator.passwordRepeatField.addEventListener('focus', function() {
            PasswordValidator.view.style.display = 'block';
        });

        PasswordValidator.passwordField.addEventListener('input', function() {
            PasswordValidator.reset();
            PasswordValidator.validatePasswordLength();
            PasswordValidator.validateSpecialChar();
            PasswordValidator.validateDigit();
            PasswordValidator.validateCapitalChar();
            PasswordValidator.validateLowercaseChar();
            PasswordValidator.validateDifferentPasswords();
        });

        PasswordValidator.passwordRepeatField.addEventListener('input', function() {
            PasswordValidator.reset();
            PasswordValidator.validatePasswordLength();
            PasswordValidator.validateSpecialChar();
            PasswordValidator.validateDigit();
            PasswordValidator.validateCapitalChar();
            PasswordValidator.validateLowercaseChar();
            PasswordValidator.validateDifferentPasswords();
        });
    };

    PasswordValidator.init();

})();
