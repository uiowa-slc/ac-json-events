<% if $Venue.Title || $Location %>
	<% if $Location %> $Location <% end_if %>
	<% if $Venue.Title %>
		<% with $Venue %>
				Location: $Title
		<% end_with %>
	<% end_if %>
<% end_if %>