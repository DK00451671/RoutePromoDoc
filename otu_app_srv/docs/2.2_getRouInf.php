<?php
<html>
	<head>
		<title></title>
	</head>
	<body>
		<p>
			<font size="18">2.2 getRouInf</font></p>
		<p>
			<span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">{</span></p>
		<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px; font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">
			<li>
				<span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p collapsible">ROU</span>&quot;</span>:<span class="object"><span class="toggle" style="cursor: pointer; border: 1px solid transparent; color: rgb(114, 159, 207);">{</span></span>
				<ul style="list-style-type: none; margin: 0px; padding: 0px 0px 0px 20px;">
					<li>
						<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">fun</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;getRouInf&quot;</span>,</span></li>
					<li>
						<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">user</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;test@test.com&quot;</span>,</span></li>
					<li>
						<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">passwd</span>&quot;</span>:<span class="string" style="color: rgb(78, 154, 6);">&quot;123&quot;</span>,</span></li>
					<li>
						<span class="object"><span class="property" style="color: rgb(32, 74, 135);">&quot;<span class="p">route_id</span>&quot;</span>:<span class="number" style="color: rgb(173, 127, 168);">401</span></span></li>
				</ul>
				<span class="object"><span class="toggle-end" style="color: rgb(114, 159, 207);">}</span></span></li>
		</ul>
		<p>
			<span class="toggle-end" style="color: rgb(114, 159, 207); font-family: 'DejaVu Sans Mono', 'Courier New', monospace; font-size: 11px; line-height: 17px; background-color: rgb(255, 255, 255);">}</span></p>
		<p>
			Test Case: &nbsp; &nbsp; &nbsp;</p>
		<p>
			&nbsp;</p>
		<table border="1">
			<tbody>
				<tr>
					<td>
						Sr no.</td>
					<td>
						&nbsp;</td>
					<td>
						status</td>
					<td>
						&nbsp;</td>
					<td>
						Description</td>
					<td>
						&nbsp;</td>
					<td>
						input data</td>
					<td>
						&nbsp;</td>
					<td>
						comment if any</td>
					<td>
						&nbsp;</td>
					<td>
						Expected Result</td>
					<td>
						&nbsp;</td>
				</tr>
				<tr>
					<td>
						1</td>
					<td>
						&nbsp;</td>
					<td>
						<b style="background-color: rgb(93, 245, 151);">ok</b></td>
					<td>
						&nbsp;</td>
					<td>
						1. user having its own route</td>
					<td>
						&nbsp;</td>
					<td>
						{&quot;ROU&quot;:{&quot;fun&quot;:&quot;getRouInf&quot;,&quot;user&quot;:&quot;test@test.com&quot;,&quot;passwd&quot;:&quot;123&quot;,&quot;route_id&quot;:2,&quot;debug&quot;:0}}</td>
					<td>
						&nbsp;</td>
					<td>
						&nbsp;</td>
					<td>
						&nbsp;</td>
					<td>
						1. should return the route information of requested route&nbsp;<br />
						{&quot;success&quot;:&quot;true&quot;,&quot;m&quot;:{&quot;is_route&quot;:{&quot;own&quot;:1},&quot;name&quot;:&quot;home1-office&quot;,&quot;owner&quot;:&quot;test@test.com&quot;,&quot;owner_id&quot;:&quot;20&quot;,&quot;co-ordinates&quot;:{&quot;2&quot;:[&quot;20.6295712028&quot;,&quot;-103.4822845459&quot;],&quot;3&quot;:[&quot;20.653024889807&quot;,&quot;-103.42666625977&quot;],&quot;4&quot;:[&quot;20.687074486194&quot;,&quot;-103.359375&quot;],&quot;5&quot;:[&quot;20.736850235082&quot;,&quot;-103.34426879883&quot;],&quot;6&quot;:[&quot;20.685147354418&quot;,&quot;-103.359375&quot;]},&quot;distance&quot;:{&quot;miles&quot;:436.88,&quot;km&quot;:703.09598559488}}}</td>
					<td>
						&nbsp;</td>
				</tr>
				<tr>
					<td>
						2</td>
					<td>
						&nbsp;</td>
					<td>
						<b style="background-color: rgb(93, 245, 151);">ok</b></td>
					<td>
						&nbsp;</td>
					<td>
						1. route is valid. Present in database.&nbsp;<br />
						2. route is not belongs to user own or sharable route.</td>
					<td>
						&nbsp;</td>
					<td>
						{&quot;ROU&quot;:{&quot;fun&quot;:&quot;getRouInf&quot;,&quot;user&quot;:&quot;test@test.com&quot;,&quot;passwd&quot;:&quot;123&quot;,&quot;route_id&quot;:23,&quot;debug&quot;:0}}</td>
					<td>
						&nbsp;</td>
					<td>
						&nbsp;</td>
					<td>
						&nbsp;</td>
					<td>
						{&quot;success&quot;:&quot;false&quot;,&quot;m&quot;:&quot;Invalid Route.&quot;}</td>
					<td>
						&nbsp;</td>
				</tr>
				<tr>
					<td>
						3</td>
					<td>
						&nbsp;</td>
					<td>
						<b style="background-color: rgb(93, 245, 151);">ok</b></td>
					<td>
						&nbsp;</td>
					<td>
						1. route is valid. Present in database.&nbsp;<br />
						2. route is not belongs to user own route but belongs to sharable route.</td>
					<td>
						&nbsp;</td>
					<td>
						{&quot;ROU&quot;:{&quot;fun&quot;:&quot;getRouInf&quot;,&quot;user&quot;:&quot;test@test.com&quot;,&quot;passwd&quot;:&quot;123&quot;,&quot;route_id&quot;:15,&quot;debug&quot;:0}}</td>
					<td>
						&nbsp;</td>
					<td>
						&nbsp;</td>
					<td>
						&nbsp;</td>
					<td>
						{&quot;success&quot;:&quot;true&quot;,&quot;m&quot;:{&quot;is_route&quot;:{&quot;shared&quot;:1},&quot;name&quot;:&quot;121&quot;,&quot;owner&quot;:&quot;jrico@gmail.com&quot;,&quot;owner_id&quot;:&quot;23&quot;,&quot;co-ordinates&quot;:{&quot;53&quot;:[&quot;20.6295712028&quot;,&quot;-103.4822845459&quot;],&quot;54&quot;:[&quot;20.653024889807&quot;,&quot;-103.42666625977&quot;],&quot;55&quot;:[&quot;20.687074486194&quot;,&quot;-103.359375&quot;],&quot;56&quot;:[&quot;20.736850235082&quot;,&quot;-103.34426879883&quot;],&quot;57&quot;:[&quot;20.685147354418&quot;,&quot;-103.359375&quot;]},&quot;distance&quot;:{&quot;miles&quot;:436.88,&quot;km&quot;:703.09598559488}}}</td>
					<td>
						&nbsp;</td>
				</tr>
				<tr>
					<td>
						4</td>
					<td>
						&nbsp;</td>
					<td>
						<b style="background-color: rgb(93, 245, 151);">ok</b></td>
					<td>
						&nbsp;</td>
					<td>
						1. Invalid user or password&nbsp;</td>
					<td>
						&nbsp;</td>
					<td>
						{&quot;ROU&quot;:{&quot;fun&quot;:&quot;getRouInf&quot;,&quot;user&quot;:&quot;test@test1.com&quot;,&quot;passwd&quot;:&quot;123&quot;,&quot;route_id&quot;:15,&quot;debug&quot;:0}}</td>
					<td>
						&nbsp;</td>
					<td>
						&nbsp;</td>
					<td>
						&nbsp;</td>
					<td>
						{&quot;success&quot;:&quot;false&quot;,&quot;m&quot;:&quot;Invalid user or password&quot;}</td>
					<td>
						&nbsp;</td>
				</tr>
				<tr>
					<td>
						5</td>
					<td>
						&nbsp;</td>
					<td>
						<b style="background-color: rgb(93, 245, 151);">ok</b></td>
					<td>
						&nbsp;</td>
					<td>
						1. invalid key user , passwd and route-id</td>
					<td>
						&nbsp;</td>
					<td>
						{&quot;ROU&quot;:{&quot;fun&quot;:&quot;getRouInf&quot;,&quot;user1&quot;:&quot;test@test.com&quot;,&quot;passwd1&quot;:&quot;123&quot;,&quot;route_id1&quot;:15,&quot;debug&quot;:1}}</td>
					<td>
						&nbsp;</td>
					<td>
						with debug flag on</td>
					<td>
						&nbsp;</td>
					<td>
						{&quot;success&quot;:&quot;false&quot;,&quot;m&quot;:&quot;route_id is missing&quot;,&quot;debug&quot;:[{&quot;send query&quot;:{&quot;fun&quot;:&quot;getRouInf&quot;,&quot;user1&quot;:&quot;test@test.com&quot;,&quot;passwd1&quot;:&quot;123&quot;,&quot;route_id1&quot;:15,&quot;debug&quot;:1}},{&quot;user&quot;:&quot;User name is missing or Invalid index&quot;},{&quot;passwd&quot;:&quot;password is missing or Invalid index&quot;},{&quot;route_id&quot;:&quot;route_id is missing or Invalid index&quot;}]}</td>
				</tr>
			</tbody>
		</table></body>
</html>

?>

