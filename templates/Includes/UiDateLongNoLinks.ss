<% with $StartDateTime %>
		<time itemprop="startDate" datetime="$Format(c)">
			$Format(l), $Format(F) $Format(j) at $Format("g:i A")
		</time>
<% end_with %>
<% if $EndTime %>
	<% with $EndTime %>
		- $Format("g:i A")
	<% end_with %>
<% end_if %>
<% if $EndDate %>
	until
	<% with $EndDate %>
			<time itemprop="endDate" datetime="$Format(c)">
			$Format("g:i A") on $Format(l), $Format(F) $Format(j)
			</time>
	<% end_with %>
<% end_if %>