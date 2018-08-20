<% if $Venue.Title || $Location %>
	<% if $Location %> $Location <% end_if %>
	<% if $Venue.Title %>
		<% with $Venue %>
			<% if $Link %>
				Location: <a href="$Link" class="button tag">$Title</a>
			<% else %>
				Location: $Title
			<% end_if %>
		<% end_with %>
	<% end_if %>
<% end_if %>