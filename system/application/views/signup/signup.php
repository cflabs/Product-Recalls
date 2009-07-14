<div id="divContent"><div class="divContainer">
<?php echo validation_errors('<div class="error">','</div>'); ?>

		<div id="divForm" style="float: none;">
			<form method="post" action="<?php echo site_url('signup'); ?>">
				<h3 class="signup">Sign Up</h3>
				<ul class="form">
					<li>
						<label for="txtEmail">Email address</label>
						<input id="txtEmail" name="txtEmail" type="text" class="textbox" value="<?php echo set_value('txtEmail'); ?>" /><br class="clear" />
					</li>
					<li>
						<label for="ddlFrequency">How often?</label>
						<select id="ddlFrequency" name="ddlFrequency" class="small">
							<option value="w" <?php echo set_select('ddlFrequency', 'w', TRUE); ?> >Weekly</option>
							<option value="m" <?php echo set_select('ddlFrequency', 'm'); ?> >Monthly</option>
						</select>
						<br class="clear" />
					</li>
					<li>
						<input type="submit" class="button" value="Create Alert &raquo;" />
					</li>
				</ul>
			</form>
		</div>
</div></div>