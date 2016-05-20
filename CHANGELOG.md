# Change Log for Codeception Redirects Module

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

- Change the check for URL exists, to just it being a 200 and not having any more redirects.

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

[Unreleased]: https://github.com/gamajo/codeception-redirects/compare/0.1.3...HEAD
[0.1.3]: https://github.com/gamajo/codeception-redirects/compare/0.1.2...0.1.3
[0.1.2]: https://github.com/gamajo/codeception-redirects/compare/0.1.1...0.1.2
[0.1.1]: https://github.com/gamajo/codeception-redirects/compare/0.1.0...0.1.1
