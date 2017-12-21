# Change Log for Codeception Redirects Module

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

_Nothing yet._

## [0.4.1] - 2017-12-21
### Changed
- Reduce minimum PHP requirement from 7.1 to 7.0.
- Tidy-up remnants of tests/ directory.
- Fix whitespace code standards. 

## [0.4.0] - 2017-12-04
### Added
- `.editorconfig`.
- `.gitattributes`.
- Code of conduct.
- Code standards checking and unit test setup.

### Changed
- `seeRedirectBetween()` to `public`. Props [Tim Jensen].
- Minor code tidying.

## [0.3.0] - 2017-10-19
### Added
- `seeTemporaryRedirectBetween()` method.

## [0.2.1] - 2017-05-12
### Added
- Security Advisories package & update composer.

### Fixed
- `followRedirects` for URL exists check.
- Home page URL no redirection check.

## [0.2.0] - 2016-12-01
### Added
- Public `urlDoesNotExist($url)` method.

### Changed
- **Breaking Change** Rename all public methods to be more descriptive.
- Public methods automatically take account of whether they should follow redirects or not.
- Public methods send a HEAD request automatically.

## [0.1.4] - 2016-09-01
- Change the check for URL exists, to just it being a 200 and not having any more redirects.
- Increase minimum version of Codeception to 2.2.0.
- Allow for `'false'` string to represent boolean `false` for check destination exists arg.

## [0.1.3] - 2016-03-25
- Improve `seePermanentRedirectTo()` to check if destination URL exists.
- Add file-level DocBlock.
- Add missing `@since` tag.
- Fix change log links.

## [0.1.2] - 2016-03-21
- Improve README.
- Use `getHeader()` method instead of fetching from an array.
- Use constants for the protocol string.
- Fix the `@since` tags.

## [0.1.1] - 2016-03-19
- Fix invalid composer.json

## 0.1.0 - 2016-03-19
- Initial release.

[Tim Jensen]: https://github.com/timothyjensen

[Unreleased]: https://github.com/gamajo/codeception-redirects/compare/0.4.1...HEAD
[0.4.1]: https://github.com/gamajo/codeception-redirects/compare/0.4.0...0.4.1
[0.4.0]: https://github.com/gamajo/codeception-redirects/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/gamajo/codeception-redirects/compare/0.2.1...0.3.0
[0.2.1]: https://github.com/gamajo/codeception-redirects/compare/0.2.0...0.2.1
[0.2.0]: https://github.com/gamajo/codeception-redirects/compare/0.1.4...0.2.0
[0.1.4]: https://github.com/gamajo/codeception-redirects/compare/0.1.3...0.1.4
[0.1.3]: https://github.com/gamajo/codeception-redirects/compare/0.1.2...0.1.3
[0.1.2]: https://github.com/gamajo/codeception-redirects/compare/0.1.1...0.1.2
[0.1.1]: https://github.com/gamajo/codeception-redirects/compare/0.1.0...0.1.1
