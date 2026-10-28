#!/usr/bin/env bash
#
# Network-activate MultilingualPress on the multisite suite's network and relate its two sites.
# Its arguments go to every wp-cli call: --path and --url in CI.
# Without the plugin this does nothing and MultilingualPressTest skips.

set -euo pipefail

# Run a command and take its "already" error on a second run as done.
once() {
    local output
    output="$("$@" 2>&1)" || grep -q 'already' <<<"$output" || { echo "$output"; exit 1; }
}

if ! wp plugin is-installed multilingualpress "$@"; then
    echo 'MultilingualPress is not installed, so its tests will skip.'
    exit 0
fi

wp plugin activate site-reviews multilingualpress --network "$@"
if ! wp site list --field=path "$@" | grep -qx '/second/'; then
    wp site create --slug=second --title='Second Site' "$@"
fi
second="$(wp eval 'echo get_blog_id_from_url(DOMAIN_CURRENT_SITE, "/second/");' "$@")"

# The integration runs only while its module is active and the sites have a language and a relation.
once wp mlp module activate site-reviews "$@"
wp eval "
    \$settings = Inpsyde\MultilingualPress\resolve(Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository::class);
    \$settings->updateLanguage('en-US', 1);
    \$settings->updateLanguage('de-DE', ${second});
" "$@"
once wp mlp relations site connect 1 "${second}" "$@"
