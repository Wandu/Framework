.PHONY: build

build:
	php ./wandu install --namespace="Wandu\\Develop"

clean:
	rm -rf app cache migrations public views src/Http
	rm -f .wandu.php
	rm -f src/ApplicationDefinition.php
	rm -f src/ApplicationServiceProvider.php
	git checkout composer.json
