<% with $StartDateTime %>
		<time itemprop="startDate" datetime="$ISOFormat">
			 $Format(EEE), $Format(MMM d), $Format("h:mm a")
		</time>
<% end_with %>
<% if $EndTime %>
	<% with $EndTime %>
		- $Format("h:mm a")
	<% end_with %>
<% end_if %>
<% if $EndDate %>
	until
	<% with $EndDate %>
			<time itemprop="endDate" datetime="$ISOFormat">
			$Format("h:mm a") $Format(MMM d)
			</time>
	<% end_with %>
<% end_if %>