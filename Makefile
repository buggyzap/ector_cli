.PHONY: help zip-me version release merge
ZIP_FILES := $(shell cat ./.zip_files)

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

	@for file in $(ZIP_FILES); do \
		cp -R $$file ./temp/$(MODULE_NAME); \
	done
	
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

