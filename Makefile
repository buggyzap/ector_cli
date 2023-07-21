.PHONY: help zip-me version release merge

# target: merge - Merge develop into master
merge:
	git checkout master
	git merge development
	git push
	git checkout development

# target: release - Create a release to github
release:
	git tag -a $(VERSION) -m "Release $(VERSION)"
	git push --tags

# target: zip-me - Create a local zip archive
zip-me: 
	@mkdir -p ./temp
	@mkdir -p ./temp/$(MODULE_NAME)
	@mkdir -p ./releases

	@cp -R ./bin temp/$(MODULE_NAME)
	@cp -R ./src temp/$(MODULE_NAME)
	@cp -R ./vendor temp/$(MODULE_NAME)

	@cp -R ./*.php temp/$(MODULE_NAME)
	@cp -R ./*.md temp/$(MODULE_NAME)
	@cp -R ./*.png temp/$(MODULE_NAME)
	@cp -R ./*.txt temp/$(MODULE_NAME)
	
	@rm -rf ./releases/$(MODULE_NAME).zip
	@cd temp && zip -rq ../releases/$(MODULE_NAME).zip $(MODULE_NAME) && cd ..
	@rm -rf ./temp

# target: version - Replace version in files
version:
	@echo "...$(VERSION)..."
	@sed -i.bak -e "s/\(VERSION = \).*/\1\'${VERSION}\';/" $(MODULE_NAME).php
	@sed -i.bak -e "s/\($this->version = \).*/\1\'${VERSION}\';/" $(MODULE_NAME).php
	@rm -f $(MODULE_NAME).php.bak config.xml.bak

# target: help - Get help on this file
help:
	@egrep "^#" Makefile

