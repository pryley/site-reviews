# Update-server captures

Real responses from the EDD Software Licensing endpoint (niftyplugins.com),
captured live on 2026-07-17 for the `site-reviews-alerts` addon at
1.0.0-beta1. These are the CONTRACT the update filters are tested against
(see ROADMAP.md, "addon-update filters").

- `get-version-valid.json` — a valid, activated licence key.
- `get-version-no-licence.json` — an EMPTY licence key: the server answers the
  version with an EMPTY `package` and `msg` "No license key has been
  provided." — the case the plugin's licence message machinery serves.
- `get-version-invalid.json` — a WRONG licence key: the version with a phantom
  NON-EMPTY `package` (confirmed live to refuse with HTTP 401 at download
  time), the refusal only in `msg` — the contract fact the ROADMAP records.
- `check-license-valid.json` / `check-license-invalid.json` — the licence
  check for the same key states.

Scrubbed before committing:

- `package` / `download_link` tokens replaced with placeholders (EDD download
  URLs embed the licence key); URL shape kept.
- `customer_email` replaced with an example.org address.
- `sections` / `raw_contents` bodies elided as "...".

`license_check` was empty in every probed scenario.

`site-reviews-alerts.php` is not a capture: it is a local plugin-file stub
whose `Version` header the transient-filter tests compare the captures'
`new_version` against.
