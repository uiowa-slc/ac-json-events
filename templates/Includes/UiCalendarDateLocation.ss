<p class="dates-times">
	<% if $Dates %>
		<% loop $Dates.Limit(1) %>
			<% with $StartDateTime %>
				<a href="{$Link}" class="date-link"><time itemprop="startDate" datetime="$ISOFormat" class="$FirstLast">$Format(M) $Format(j)</time></a>
			<% end_with %>
			<% if $EndDate %>
			 - 
			<% with $EndDate %>
				<a href="{$Link}" class="date-link"><time itemprop="endDate" datetime="$ISOFormat" class="$FirstLast">$Format(M) $Format(j)</time></a>
			<% end_with %>
			<% end_if %>
		<% end_loop %>
	<% end_if %>
	<% if Location %>{$Location}<% end_if %>
	<% if $Venue %>
		<% with $Venue %>
			<span> @ </span>
				<span itemprop="location" itemscope itemtype="http://data-vocabulary.org/​Organization">
			<a href="$Link" class="tag button" ><span itemprop="name">$Title.LimitCharacters(25)</span></a>
		</span>	
		<% end_with %>
	<% end_if %>	
	<% if $Dates.Count > "1" %>
		<a href="$Link" class="button more-dates">more dates</a> 
	<% end_if %> 
</p>