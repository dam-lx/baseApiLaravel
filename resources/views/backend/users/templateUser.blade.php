<!--template table-->
<div class="#template" style="display: none;">
	<table >
		<tbody id="data-content">
			<tr>
				<td align="center" class="check-delete animated-checkbox">
					<label>
						<input class="check" type="checkbox" name="xoa[]"><span class="label-text"></span>
					</label>
				</td>
				<td><img class="img" src="" alt="Avatar"></td>
				<td class="name"></td>
				<td class="birthdate"></td>
				<td class="gender"></td>
				<td class="email"></td>
				<td class="role"></td>
				<td class="active"></td>
				<td>
					<button type="button" class="btn btn-primary edit round" data-toggle="tooltip"
							title="{{trans("label.edit")}}">
					  <i class="fa fa-pencil-square-o"></i>
					</button>
					<button type="button" class="btn btn-danger round delete" data-toggle="tooltip"
							title="{{trans("label.delete")}}">
						<i class="fa fa-trash-o"></i>
					</button>
				</td>
			</tr>
		</tbody>	
	</table>
</div>