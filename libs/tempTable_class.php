<?php

class tempTable
{
    private $mysql;

    function __construct($mysql)
    {
        $this->mysql = $mysql;
    }

    function criaTmpTable()
    {
        $sql = "
		CREATE TEMPORARY TABLE IF NOT EXISTS tmp (
			`dias` VARCHAR(10),
			`tipo` VARCHAR(10),
			`FI_CXA` decimal(5,0) unsigned NOT NULL DEFAULT '0',
			`FI_DTA` decimal(9,0) unsigned NOT NULL DEFAULT '0',
			`FI_LIN` decimal(5,0) unsigned NOT NULL DEFAULT '0',
			`FI_TDC` varchar(29) NOT NULL DEFAULT ' ',
			`FI_VAL` decimal(13,2) NOT NULL DEFAULT '0.00',
			`FI_VDD` decimal(13,2) NOT NULL DEFAULT '0.00',
			`FI_LAN` decimal(9,0) unsigned NOT NULL DEFAULT '0',
			`FI_ADT` varchar(1) NOT NULL DEFAULT ' ',
			`dta_mod` ENUM('s', 'n', 'w') default 'n',
			`MV_TIP` char(1) NOT NULL DEFAULT '' COMMENT ' Tipo da duplicata \"1, 2, 3 e 4\" - A pagar',
			`MV_NUM` decimal(11,0) unsigned NOT NULL DEFAULT '0' COMMENT ' Numero da Duplicata',
			`MV_NBC` varchar(25) NOT NULL DEFAULT '',
			`MV_REP` decimal(5,0) unsigned NOT NULL DEFAULT '0' COMMENT ' Código do vendedor/representante',
			`MV_VLV` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT ' Valor original no vencimento',
			`MV_CAD` decimal(5,0) unsigned NOT NULL DEFAULT '0' COMMENT ' Cadastro do Cliente',
			`MV_CTA` decimal(5,0) unsigned NOT NULL DEFAULT '0' COMMENT ' Operacoes',
			`MV_CDES` decimal (1,0) unsigned NOT NULL DEFAULT '0' COMMENT ' Descontado',
			`MV_FAT` decimal(11,0) unsigned NOT NULL DEFAULT '0' COMMENT ' Numero da Fatura',
			`MV_VCT` decimal(9,0) unsigned NOT NULL DEFAULT '0' COMMENT ' Vencimento da duplicata',
			
            `PRE_DTA` decimal(9,0) unsigned NOT NULL DEFAULT '0',
            `PRE_REP` INT(11) NULL DEFAULT NULL,
            `PRE_CAD` VARCHAR(35) NOT NULL DEFAULT ' ',
            `PRE_VLR` DECIMAL(14,2) NOT NULL DEFAULT '0'
		) ENGINE=MEMORY;
		";
        $rs = $this->mysql->query($sql);
    }

    function criaTmpTable2()
    {
        $sql = "
		CREATE TABLE IF NOT EXISTS tmp (
			`dias` VARCHAR(10),
			`tipo` VARCHAR(10),
			`FI_CXA` decimal(5,0) unsigned NOT NULL DEFAULT '0',
			`FI_DTA` decimal(9,0) unsigned NOT NULL DEFAULT '0',
			`FI_LIN` decimal(5,0) unsigned NOT NULL DEFAULT '0',
			`FI_TDC` varchar(29) NOT NULL DEFAULT ' ',
			`FI_VAL` decimal(13,2) NOT NULL DEFAULT '0.00',
			`FI_VDD` decimal(13,2) NOT NULL DEFAULT '0.00',
			`FI_LAN` decimal(9,0) unsigned NOT NULL DEFAULT '0',
			`FI_ADT` varchar(1) NOT NULL DEFAULT ' ',
			`MV_TIP` char(1) NOT NULL DEFAULT '' COMMENT ' Tipo da duplicata \"1, 2, 3 e 4\" - A pagar',
			`MV_NUM` decimal(7,0) unsigned NOT NULL DEFAULT '0' COMMENT ' Numero da Duplicata',
			`MV_REP` decimal(5,0) unsigned NOT NULL DEFAULT '0' COMMENT ' Código do vendedor/representante',
			`MV_VLV` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT ' Valor original no vencimento',
			`MV_CAD` decimal(5,0) unsigned NOT NULL DEFAULT '0' COMMENT ' Cadastro do Cliente'
		) ENGINE=MEMORY;
		";
        $rs = $this->mysql->query($sql);
    }

    function truncaTmpTable()
    {
        $sql = "TRUNCATE TABLE tmp";
        $rs = $this->mysql->query($sql);
    }

    function deletaTmpTable()
    {
        $sql = "DROP TABLE tmp;";
        $rs = $this->mysql->query($sql);
    }

}