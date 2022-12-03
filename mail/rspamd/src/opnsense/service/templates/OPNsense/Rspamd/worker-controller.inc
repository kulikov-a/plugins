{% if helpers.exists('OPNsense.Rspamd.general.enabled') and OPNsense.Rspamd.general.enabled == '1' %}
{%   if helpers.exists('OPNsense.Rspamd.controller.bind_socket') and OPNsense.Rspamd.controller.bind_socket != '' %}
{%     for address in OPNsense.Rspamd.controller.bind_socket.split(',') %}
bind_socket = "{{ address }}";
{%     endfor %}
{%   endif %}
{%   if helpers.exists('OPNsense.Rspamd.controller.secure_ip') and OPNsense.Rspamd.controller.secure_ip != '' %}
{%     for ip in OPNsense.Rspamd.controller.secure_ip.split(',') %}
secure_ip = "{{ ip }}";
{%     endfor %}
{%   endif %}
{%   if helpers.exists('OPNsense.Rspamd.controller.password') and OPNsense.Rspamd.controller.password != '' %}
{%     if OPNsense.Rspamd.controller.password.startswith('HASH: ') %}
password = "{{ OPNsense.Rspamd.controller.password[6:] }}";
{%     else %}
# password value error.
{%     endif %}
{%   endif %}
{%   if helpers.exists('OPNsense.Rspamd.controller.enable_password') and OPNsense.Rspamd.controller.enable_password != '' %}
{%     if OPNsense.Rspamd.controller.enable_password.startswith('HASH: ') %}
enable_password = "{{ OPNsense.Rspamd.controller.enable_password[6:] }}";
{%     else %}
# enable_password value error.
{%     endif %}
{%   endif %}
{% endif %}
