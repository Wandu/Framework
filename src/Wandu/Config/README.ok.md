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

- PHP (example, [test_php.php](../../../tests/Config/test_php.php))
- JSON (example, [test_json.json](../../../tests/Config/test_json.json))
- Env(require `m1/env`) (example, [test_env.env](../../../tests/Config/test_env.env))
- YAML(require `symfony/yaml`) (example, [test_yml.yml](../../../tests/Config/test_yml.yml))

@code("../../../tests/Config/ReadmeTest.php@support-loader")
