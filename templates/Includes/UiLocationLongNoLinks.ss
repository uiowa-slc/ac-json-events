<% if Location %>{$Location}<% if $Venue %>, <% end_if %><% end_if %>
	<% if $Venue %>
		<% with $Venue %>
		$Title
		<% end_with %>
	<% end_if %>	