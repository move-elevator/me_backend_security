# TYPO3 Extension "me_backend_security"

An extension to specify secure password rules and force backend users to change their passwords, if the password is older then a defined limit.

In extension settings you can define:
* Minimum number of capital characters
* Minimum number of lowercase characters
* Minimum number of digits
* Minimum number of special characters
* Minimum length of password
* Maximum days before password must change

If a backend user logs in and his password needs to change, user will be logged out and redirected to comfortable password change form.
After password was changed, the user will automatically logged in and can start his work in backend.

The password rules will also be checked when the user tries to change his password in user settings in backend.

Users imported from extension ig_ldap_sso_auth will be ignored.

## Install and usage

1. Basic install via composer

    ```
    composer req "move-elevator/me-backend-security":"^3.0"
    ```
2. Modify extension settings in TYPO3 backend
3. Be safer :)

## Checks
Run each command in the project root directory.

### Execute PHPUnit tests

```
composer phpunit
```

### Execute PHPCS checks

```
composer phpcs
```

### Fix PHPCS issues

```
composer phpcs:fix
```

### Execute PHPSTAN checks

```
composer phpstan
```

### Execute all quality checks

```
composer php:validate
```

# Contact

* Mail: typo3@move-elevator.de
* Website: https://www.move-elevator.de

# Changelog
2022-06-22 - Ronny Hauptvogel <rh@move-elevator.de>
```
Release 3.0.1
---
Bugfix: Fix FrontendBackendUserAuthentication issue which will break third-party extension ext_localconf.php files
```

2022-05-19 - Ronny Hauptvogel <rh@move-elevator.de>
```
Release 3.0.0
---
Feature: Add TYPO3 11 compatibility
Feature: PHP 8.0/ 8.1 compatibility
Feature: Drop TYPO3 9 and 10 support
```

2021-12-30 - Ronny Hauptvogel <rh@move-elevator.de>
```
Release 2.0.7
---
Feature: Add password validation rules to password reset form
```

2021-07-19 - Ronny Hauptvogel <rh@move-elevator.de>
```
Release 2.0.6
---
Bugfix: Fix broken language keys
```

2021-04-23 - Ronny Hauptvogel <rh@move-elevator.de>
```
Release 2.0.5
---
Feature: Add .gitattributes file
Feature: Add composer extension key
Bugfix: Add proper type casting for hooks
```

2020-12-17 - Ronny Hauptvogel <rh@move-elevator.de>
```
Release 2.0.4
---
Feature: Add typo3/cms-rsaauth as conflict
```

2020-12-09 - Ronny Hauptvogel <rh@move-elevator.de>
```
Release 2.0.3
---
Bugfix: Update PHP version constraint to include newer PHP7 versions
```

2020-11-06 - Ronny Hauptvogel <rh@move-elevator.de>
```
Release 2.0.2
---
Feature: TYPO3 10 compatibility
Feature: Allow new special characters to validator
Bugfix: Invalid password length with german umlauts
```

2019-05-03 - Philipp Heckelt <phe@move-elevator.de>
```
Release 2.0.1
---
Bugfix: PHP7 typehints for hooks
```

2019-04-08 - Philipp Heckelt <phe@move-elevator.de>
```
Release 2.0.0
---
Feature: TYPO3v9 compatibility
```

2018-08-06 - Philipp Heckelt <phe@move-elevator.de>
```
Release 1.1.4
---
Bugfix: Fix detection of existing accounts
```

2018-02-01 - Philipp Heckelt <phe@move-elevator.de>
```
Release 1.1.3
---
Bugfix: Validate that the old and new password are not the same in the user settings
Bugfix: Existing accounts no longer must change their password immediately after activating the extension
Feature: Allow new special characters to validator
```

2018-01-18 - Philipp Heckelt <phe@move-elevator.de>
```
Release 1.1.2
---
Bugfix: Fix redirect on non-ssl websites if password change is required
```

2018-01-10 - Philipp Heckelt <phe@move-elevator.de>
```
Release 1.1.1
---
Bugfix: Fix hook exception in frontend mode on active backend user login
```

2017-12-22 - Philipp Heckelt <phe@move-elevator.de>
```
Release 1.1.0
---
Feature: Optimized validator error messages
Feature: Validator for same passwords
Feature: Different Message for first password change
```

2017-12-04 - Philipp Heckelt <phe@move-elevator.de>
```
Release 1.0.4
---
Feature: Use internal database connection for TYPOv8 compatibility, remove database connection factory
```

2017-12-08 - Philipp Heckelt <phe@move-elevator.de>
```
Release 1.0.3
---
Bugfix: Optional port in database configuration
```

2017-12-08 - Philipp Heckelt <phe@move-elevator.de>
```
Release 1.0.2
---
Bugfix: Language service
```

2017-12-08 - Philipp Heckelt <phe@move-elevator.de>
```
Release 1.0.1
---
Bugfix: Extension configuration
```

2017-12-04 - Philipp Heckelt <phe@move-elevator.de>
```
Release 1.0.0
```

# Roadmap
* Blacklist for usernames like "admin"
