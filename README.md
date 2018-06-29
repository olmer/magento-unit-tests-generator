# magento-unit-tests-generator
Sometimes writing new unit test for class with multiple dependencies may be tedious, so this package is intended to simplify magento2 unit tests creation. Command reads source file and generate basic unit test structure for specified class. If unit test already exists - nothing will happen. Test class is placed into test object class' module under app/code/Vendor/Module/Test/Unit/...

### How to install

```bash
composer require olmer/magento-unit-tests-generator:^0.2 --dev
php bin/magento cache:clean
php bin/magento setup:di:compile
```

### How to generate a unit test for a specific class

```bash
php bin/magento dev:tests:generate-unit /path/to/class/file.php
```
