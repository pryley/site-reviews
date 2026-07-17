# Update-server captures

Real responses from the EDD Software Licensing endpoint (niftyplugins.com),
captured 2026-07-17 by Paul from a licensed install running the
`site-reviews-actions` addon at 1.0.0-beta12. These are the CONTRACT the
update filters are tested against (see ROADMAP.md, "addon-update filters").

Scrubbed before committing:

- `package` / `download_link` tokens replaced with placeholders (EDD download
  URLs carry a licence/expiry token); URL shape kept.
- `customer_email` replaced with an example.org address.
- `sections` / `raw_contents` bodies elided as "..." in the capture itself.

The notable contract fact: `get_version` answers a NON-EMPTY `package` even
for an invalid licence (the URL just refuses at download time), with the
refusal only in `msg` — a field the plugin's Defaults drop.

`site-reviews-actions.php` is not a capture: it is a local plugin-file stub
whose `Version` header the transient-filter tests compare the capture's
`new_version` against.

## Second capture round (2026-07-17, live probe against niftyplugins.com)

`get-version-no-licence.json` — a get_version request with an EMPTY licence key
(addon `site-reviews-alerts` at 1.0.0-beta1): the server answers the version
with an EMPTY `package` and `msg` "No license key has been provided." — so the
plugin's licence message machinery works as designed for a MISSING key. The
phantom non-empty package occurs only for a WRONG key (get-version-invalid.json),
and that URL was confirmed live to refuse with HTTP 401 at download time.
`license_check` was empty in every scenario probed.
