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
