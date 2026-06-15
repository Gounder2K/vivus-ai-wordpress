#!/usr/bin/env bash
#
# Idempotent provisioning for the Vivus AI WordPress site.
# Runs inside the wp-cli container after the DB + WordPress services are up.
#
# Safe to run repeatedly — it checks state before each step.

set -euo pipefail

WP="wp --path=/var/www/html"

echo "⏳ Waiting for the database to accept connections…"
until $WP db check >/dev/null 2>&1; do
  sleep 3
done

# ---------------------------------------------------------------------------
# 1) Core install
# ---------------------------------------------------------------------------
if ! $WP core is-installed >/dev/null 2>&1; then
  echo "🚀 Installing WordPress…"
  $WP core install \
    --url="${WP_URL}" \
    --title="${WP_TITLE}" \
    --admin_user="${WP_ADMIN_USER}" \
    --admin_password="${WP_ADMIN_PASSWORD}" \
    --admin_email="${WP_ADMIN_EMAIL}" \
    --skip-email
else
  echo "✅ WordPress already installed."
fi

# Make sure the site URL matches the mapped port.
$WP option update siteurl "${WP_URL}"
$WP option update home "${WP_URL}"
$WP option update blogdescription "AI assistant for clinical guidance and medical workflow support."

# Pretty permalinks (also makes the REST endpoint clean).
$WP rewrite structure '/%postname%/' --hard >/dev/null 2>&1 || true

# ---------------------------------------------------------------------------
# 2) Theme + plugin
# ---------------------------------------------------------------------------
echo "🎨 Activating the Vivus AI theme…"
$WP theme activate vivus-ai

echo "🔌 Activating the Vivus Leads plugin…"
$WP plugin activate vivus-leads

# Remove the bundled sample content so the demo looks clean.
$WP post delete 1 --force >/dev/null 2>&1 || true   # "Hello world!" post
$WP post delete 2 --force >/dev/null 2>&1 || true   # "Sample Page"

# ---------------------------------------------------------------------------
# 3) Pages (create once, by slug)
# ---------------------------------------------------------------------------
create_page () {
  local title="$1"; local slug="$2"; local content="$3"
  local existing
  existing="$($WP post list --post_type=page --name="${slug}" --field=ID 2>/dev/null || true)"
  if [ -z "${existing}" ]; then
    echo "📄 Creating page: ${title}"
    $WP post create --post_type=page --post_status=publish \
      --post_title="${title}" --post_name="${slug}" --post_content="${content}"
  else
    echo "✅ Page already exists: ${title}"
  fi
}

create_page "Home" "home" "<!-- The front page is rendered by the theme's front-page.php sections. -->"

ABOUT_CONTENT='<p class="lead">Vivus AI started with a simple frustration: clinicians spend too much of their day fighting software instead of caring for patients.</p><p>We build an AI assistant that respects clinical work — fast, grounded, and private by design. Vivus AI runs against models you control, turns questions into structured answers, and produces patient-ready documents in seconds.</p><h2>Our mission</h2><p>To give every care team a calm, trustworthy assistant that gives them their time back — without ever compromising on privacy or safety.</p>'
create_page "About" "about" "${ABOUT_CONTENT}"

CONTACT_CONTENT='<p class="lead">Want to see Vivus AI in your workflow? Send us a note and we will set up a tailored walkthrough.</p>'
create_page "Contact" "contact" "${CONTACT_CONTENT}"

# Set the static front page.
HOME_ID="$($WP post list --post_type=page --name=home --field=ID)"
$WP option update show_on_front page
$WP option update page_on_front "${HOME_ID}"

# Contact email used by the theme footer / contact aside.
$WP option update theme_mods_vivus-ai '{"vivus_contact_email":"hello@vivus.ai"}' --format=json >/dev/null 2>&1 || true

# ---------------------------------------------------------------------------
# 4) Primary navigation menu
# ---------------------------------------------------------------------------
MENU_NAME="Primary"
if ! $WP menu list --fields=name 2>/dev/null | grep -q "^${MENU_NAME}$"; then
  echo "🧭 Building primary menu…"
  $WP menu create "${MENU_NAME}"
  $WP menu item add-custom "${MENU_NAME}" "Features"     "${WP_URL}/#features"     >/dev/null
  $WP menu item add-custom "${MENU_NAME}" "How it works" "${WP_URL}/#how-it-works" >/dev/null
  $WP menu item add-custom "${MENU_NAME}" "Demo"         "${WP_URL}/#demo"         >/dev/null
  $WP menu item add-custom "${MENU_NAME}" "Pricing"      "${WP_URL}/#pricing"      >/dev/null
  ABOUT_ID="$($WP post list --post_type=page --name=about --field=ID)"
  CONTACT_ID="$($WP post list --post_type=page --name=contact --field=ID)"
  $WP menu item add-post "${MENU_NAME}" "${ABOUT_ID}"   >/dev/null
  $WP menu item add-post "${MENU_NAME}" "${CONTACT_ID}" >/dev/null
  $WP menu location assign "${MENU_NAME}" primary
else
  echo "✅ Primary menu already exists."
fi

# Seed a couple of demo leads so the admin dashboard isn't empty.
SEED_FLAG="$($WP option get vivus_leads_seeded 2>/dev/null || true)"
if [ -z "${SEED_FLAG}" ]; then
  echo "🌱 Seeding demo leads…"
  # Default install uses the wp_ table prefix.
  $WP db query "INSERT INTO wp_vivus_leads
    (name,email,organisation,team_size,message,status,user_agent,created_at) VALUES
    ('Dr. Amara Okafor','amara@northsideclinic.example','Northside Clinic','6-20','We are after a clinical assistant our compliance team is happy with.','new','seed',NOW()),
    ('James Whitlock','james@meridianhealth.example','Meridian Health','100+','Interested in self-hosted models for our hospital group.','contacted','seed',NOW());" >/dev/null 2>&1 || true
  $WP option update vivus_leads_seeded 1 >/dev/null 2>&1 || true
fi

echo ""
echo "🎉 Vivus AI is ready at ${WP_URL}"
echo "   Admin: ${WP_URL}/wp-admin  (user: ${WP_ADMIN_USER} / pass: ${WP_ADMIN_PASSWORD})"
