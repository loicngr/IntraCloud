DIR_APP := ./app
DIR_API := ./api
DIR_APP_NODE_MODULES := $(DIR_APP)/node_modules
DIR_APP_NPM := $(DIR_APP)/package-lock.json
DIR_API_NODE_MODULES := $(DIR_API)/node_modules
DIR_API_NPM := $(DIR_API)/package-lock.json

install:
	cd $(DIR_API) && npm i && composer install
	cd $(DIR_APP) && npm i

clean-install:
	make -s clean
	cd $(DIR_API) && npm i && composer install
	cd $(DIR_APP) && npm i

build:
	cd $(DIR_APP) && npm run build

update-build:
	git pull
	make build

clean:
	rm -rf $(DIR_APP_NODE_MODULES) $(DIR_APi_NODE_MODULES)
