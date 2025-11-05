# Contributing to Manta Products

Thank you for your interest in contributing to Manta Products! 🎉

## 🚀 How can you contribute?

### Bug Reports
- Use the [Bug Report template](.github/ISSUE_TEMPLATE/bug_report.md)
- Describe the problem as detailed as possible
- Add code examples if possible
- Mention your Laravel and PHP version

### Feature Requests  
- Use the [Feature Request template](.github/ISSUE_TEMPLATE/feature_request.md)
- Explain why this feature would be useful
- Give examples of how it should work

### Pull Requests
1. **Fork** the repository
2. **Create a branch** for your feature: `git checkout -b feature/amazing-feature`
3. **Commit** your changes: `git commit -m 'Add amazing feature'`
4. **Push** to your branch: `git push origin feature/amazing-feature`
5. **Open a Pull Request**

## 📋 Development Guidelines

### Code Style
- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards
- Use meaningful variable and method names
- Add docblocks for all public methods
- Keep methods short and focused

### Testing
```bash
# Run tests
composer test

# Run tests with coverage
composer test-coverage

# Run static analysis
composer analyse
```

### Database Migrations
- Use descriptive migration names
- Test migrations both up and down
- Document breaking changes in CHANGELOG.md

## 🔧 Development Setup

```bash
# Clone the repository
git clone https://github.com/ArvidDeJong/manta-products.git
cd manta-products

# Install dependencies
composer install

# Run tests
composer test
```

## 📝 Commit Messages

Use clear commit messages:

```
feat: add support for custom SKU patterns
fix: resolve variant pricing calculation bug  
docs: update installation instructions
refactor: simplify VariantMatrixService logic
test: add unit tests for Product model
```

Prefixes:
- `feat`: new feature
- `fix`: bug fix
- `docs`: documentation changes
- `style`: code formatting
- `refactor`: code refactoring
- `test`: add/modify tests
- `chore`: maintenance tasks

## 🤝 Code of Conduct

- Be respectful and constructive
- Help others where possible
- Accept feedback gracefully
- Focus on improving the package

## ❓ Questions?

- Open a [Discussion](https://github.com/ArvidDeJong/manta-products/discussions)
- Send an email to [info@arvid.nl](mailto:info@arvid.nl)

Thank you for your contribution! 🙏
