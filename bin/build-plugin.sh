#!/bin/bash

printf "Plugin name: "
read -r NAME

printf "Destination folder: "
read -r FOLDER

printf "Include Grunt support (Y/n): "
read -r GRUNT

printf "Initialize new git repo (y/N): "
read -r NEWREPO

set -e

DEFAULT_NAME="WP Plugin Template"
DEFAULT_CLASS=${DEFAULT_NAME// /_}
DEFAULT_TOKEN=$( tr '[A-Z]' '[a-z]' <<< "$DEFAULT_CLASS")
DEFAULT_SLUG=${DEFAULT_TOKEN//_/-}

CLASS=${NAME// /_}
TOKEN=$( tr '[A-Z]' '[a-z]' <<< "$CLASS")
SLUG=${TOKEN//_/-}

if [ -n "$FOLDER" ]; then
	if [ ! -d "$FOLDER/$SLUG" ]; then
		mkdir -p "$FOLDER"

		git clone "https://github.com/Monogramm/$DEFAULT_SLUG.git" "$FOLDER/$SLUG"
	fi

	cd "$FOLDER/$SLUG"
fi

if [ "$NEWREPO" == "y" ]; then
	echo "Removing git files..."
	rm -rf .git
fi

rm bin/build-plugin.sh

if [ "$GRUNT" == "n" ]; then
	rm Gruntfile.js
	rm package.json
fi

echo "Updating plugin files..."

mv "${DEFAULT_SLUG}.php" "${SLUG}.php"

mv "lang/${DEFAULT_SLUG}.pot" "lang/${SLUG}.pot"

mv "includes/class-${DEFAULT_SLUG}.php" "includes/class-${SLUG}.php"
mv "includes/class-${DEFAULT_SLUG}-settings.php" "includes/class-${SLUG}-settings.php"
mv "includes/class-${DEFAULT_SLUG}-shortcodes.php" "includes/class-${SLUG}-shortcodes.php"

mv "includes/lib/class-${DEFAULT_SLUG}-post-type.php" "includes/lib/class-${SLUG}-post-type.php"
mv "includes/lib/class-${DEFAULT_SLUG}-taxonomy.php" "includes/lib/class-${SLUG}-taxonomy.php"
mv "includes/lib/class-${DEFAULT_SLUG}-admin-api.php" "includes/lib/class-${SLUG}-admin-api.php"

mv "includes/lib/class-${DEFAULT_SLUG}-admin-api.php" "includes/shortcodes/class-${SLUG}-shortcode-powered-by.php"

mv "tests/${DEFAULT_CLASS}_Test.php" "tests/${CLASS}.php"

for f in README.md readme.txt .gitmoji-changelogrc composer.json Gruntfile.js manage.sh package.json 'index.php' 'uninstall.php' 'bootstrap.php' "$SLUG.php" assets/js/*.js "lang/$SLUG.pot" "includes/index.php" "includes/class-$SLUG.php" "includes/class-$SLUG-settings.php" "includes/class-$SLUG-shortcodes.php" "includes/lib/class-$SLUG-post-type.php" "includes/lib/class-$SLUG-taxonomy.php" "includes/lib/class-$SLUG-admin-api.php" "includes/shortcodes/class-${SLUG}-shortcode-powered-by.php" "tests/$CLASS.php" 'tests/SampleTest.php' '.env' '.travis.yml' '.phpcs.xml.dist' '.github/PULL_REQUEST_TEMPLATE.md' '.gitlab/merge_request_templates/merge_request_template.md' '.gitpod.Dockerfile'
do
	cp "$f" "$f.tmp"
	sed "s/${DEFAULT_NAME}/${NAME}/g" "$f.tmp" > "$f"
	rm "$f.tmp"

	cp "$f" "$f.tmp"
	sed "s/${DEFAULT_SLUG}/${SLUG}/g" "$f.tmp" > "$f"
	rm "$f.tmp"

	cp "$f" "$f.tmp"
	sed "s/${DEFAULT_TOKEN}/${TOKEN}/g" "$f.tmp" > "$f"
	rm "$f.tmp"

	cp "$f" "$f.tmp"
	sed "s/${DEFAULT_CLASS}/${CLASS}/g" "$f.tmp" > "$f"
	rm "$f.tmp"
done

if [ "${NEWREPO}" == "y" ]; then
	echo "Initialising new git repo..."
	git init
fi

echo "Complete!"
