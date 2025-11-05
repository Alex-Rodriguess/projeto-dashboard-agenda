<form id="form1" name="form1" class="form" role="form" action="agendaPrevisoes.php" method="post">
	<tr>
		<td>
            <input type="text" id="data" name="data" class="form-control" required placeholder="Dta Vencimento">
        </td>
		<td>
			<select id="reresentante" name="representante" class="form-control">
				<option value="">Selecione um representante</option>
				<?php
				$representantes = $estven -> listaRep();
				foreach ($representantes as $rep_id => $rep_nome) {
				?>
				<option value="<?= $rep_id ?>"><?= $rep_nome ?></option>
				<?php
				}
 				?>
			</select>
			</td>
		<td>
			<select id="cliente" name="cliente" class="form-control" required>
				<option value="">Selecione um cliente</option>
				<?php
				$clientes = $scccad -> listCli();
				foreach ($clientes as $cli_id => $cli_nome) {
				?>
				<option value="<?= $cli_id ?>"><?= $cli_nome ?></option>
				<?php
				}
			?>
			</select>
		</td>
	<td>
		<div class="input-group">
			<span class="input-group-addon">R$</span><input id="valor" name="valor" type="text" class="form-control" value="0,00" required />
		</div>
	</td>
	</tr>
	<tr>
		<td colspan="6" class="text-center">
			<input id="submit" name="submit" type="submit" value="Salvar" class="btn btn-success" />
			<input id="reset" name="reset" type="button" value="Limpar" class="btn btn-default" />
		</td>
	</tr>
</form>