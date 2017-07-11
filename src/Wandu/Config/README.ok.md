Wandu Config
===

[![Latest Stable Version](https://poser.pugx.org/wandu/config/v/stable.svg)](https://packagist.org/packages/wandu/config)
[![Latest Unstable Version](https://poser.pugx.org/wandu/config/v/unstable.svg)](https://packagist.org/packages/wandu/config)
[![Total Downloads](https://poser.pugx.org/wandu/config/downloads.svg)](https://packagist.org/packages/wandu/config)
[![License](https://poser.pugx.org/wandu/config/license.svg)](https://packagist.org/packages/wandu/config)

Simple Config Based On Dot Array.

## Installation

```bash
composer require wandu/config
```

## Usage

@code("../../../tests/Config/ReadmeTest.php@basic-usage")

### Use Default Value

@code("../../../tests/Config/ReadmeTest.php@get-default-value")

### Support Loader

- PHP (example, [test.config.php](../../../tests/Config/test.config.php))
- JSON (example, [test.config.json](../../../tests/Config/test.config.json))
- Env(require `m1/env`) (example, [test.config.env](../../../tests/Config/test.config.env))
- YAML(require `symfony/yaml`) (example, [test.config.yml](../../../tests/Config/test.config.yml))

@code("../../../tests/Config/ReadmeTest.php@support-loader")
