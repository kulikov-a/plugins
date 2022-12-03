# Please don't modify this file as your changes might be overwritten with
# the next update.
#
type = "syslog";
facility = "mail";
systemd = true;
{% if helpers.exists('OPNsense.Rspamd.general.log_level') and OPNsense.Rspamd.general.log_level != '' %}
level = "{{ OPNsense.Rspamd.general.log_level }}";
#debug_modules = ["antivirus"];
{% endif %}
