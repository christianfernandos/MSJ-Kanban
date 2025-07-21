<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class example_tabel_form_master extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sys_table')->where(['gmenu' => 'exampl', 'dmenu' => 'msfrmm'])->delete();

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '1',
            'field' => 'id',
            'alias' => 'ID',
            'type' => 'hidden',
            'length' => '20',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:20|min:3',
            'primary' => '1',
            'filter' => '0',
            'list' => '1',
            'show' => '0',
            'class' => '',
            'query' => '',
            'note' => '',
            'position' => '3'
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '2',
            'field' => 'upper',
            'alias' => 'Upper',
            'type' => 'string',
            'length' => '20',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:20|min:3',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'class' => 'upper',
            'query' => '',
            'note' => 'Contoh Class upper',
            'position' => '3'
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '3',
            'field' => 'lower',
            'alias' => 'Lower',
            'type' => 'string',
            'length' => '20',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:20|min:3',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'class' => 'lower',
            'query' => '',
            'note' => 'Contoh Class lower',
            'position' => '3'
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '4',
            'field' => 'notspace',
            'alias' => 'NotSpace',
            'type' => 'string',
            'length' => '20',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:20|min:3',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'class' => 'notspace',
            'query' => '',
            'note' => 'Contoh Class notspace',
            'position' => '3'
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '5',
            'field' => 'readonly',
            'alias' => 'Readonly',
            'type' => 'string',
            'length' => '20',
            'decimals' => '0',
            'default' => 'readonly',
            'validate' => 'required|max:20|min:3',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'class' => 'readonly',
            'query' => '',
            'note' => 'Contoh Class readonly',
            'position' => '3'
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '6',
            'field' => 'customs',
            'alias' => 'Custom-Select',
            'type' => 'enum',
            'length' => '20',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:20|min:1',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'class' => 'custom-select',
            'query' => "select value, name from sys_enum where idenum = 'layout'",
            'note' => 'Contoh Class custom-select',
            'position' => '3'
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '7',
            'field' => 'multiples',
            'alias' => 'Multiple-Select',
            'type' => 'enum',
            'length' => '20',
            'decimals' => '0',
            'default' => 'multiples',
            'validate' => 'required|max:20|min:1',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'class' => 'select-multiple custom-select',
            'query' => "select value, name from sys_enum where idenum = 'layout'",
            'note' => 'Contoh Class select-multiple',
            'position' => '3'
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '8',
            'field' => 'char',
            'alias' => 'Char',
            'type' => 'string',
            'length' => '20',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:20|min:3',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'class' => '',
            'query' => '',
            'note' => 'Contoh Type char',
            'position' => '4'
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '9',
            'field' => 'currency',
            'alias' => 'Currency',
            'type' => 'currency',
            'length' => '9999999999',
            'decimals' => '2',
            'default' => '',
            'validate' => 'required|numeric|between:1,9999999999',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'class' => '',
            'query' => '',
            'sub' => 'IDR ',
            'note' => 'Contoh Type currency',
            'position' => '4'
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '10',
            'field' => 'date',
            'alias' => 'Date',
            'type' => 'date',
            'length' => '0',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'class' => '',
            'query' => '',
            'note' => 'Contoh Type date',
            'position' => '4'
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '11',
            'field' => 'email',
            'alias' => 'Email',
            'type' => 'email',
            'length' => '255',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:255|min:3',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'class' => '',
            'query' => '',
            'note' => 'Contoh Type email',
            'position' => '4'
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '12',
            'field' => 'enum',
            'alias' => 'Enum',
            'type' => 'enum',
            'length' => '20',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:20|min:3',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'class' => 'enum',
            'query' => "select value, name from sys_enum where idenum = 'layout'",
            'note' => 'Contoh Type enum',
            'position' => '4'
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '13',
            'field' => 'file',
            'alias' => 'File',
            'type' => 'file',
            'length' => '5120',
            'decimals' => '0',
            'default' => '',
            'validate' => 'mimes:pdf,PDF,xls,XLS,xlsx,XLSX,png,PNG,jpg,JPG,jpeg,JPEG|file|max:5120',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'class' => '',
            'query' => '',
            'note' => 'Contoh Type file',
            'position' => '4'
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '14',
            'field' => 'image',
            'alias' => 'Image',
            'type' => 'image',
            'length' => '5120',
            'decimals' => '0',
            'default' => 'noimage.png',
            'validate' => 'mimes:png,PNG,jpg,JPG,jpeg,JPEG|file|max:5120',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'class' => '',
            'query' => '',
            'note' => 'Contoh Type image',
            'position' => '4'
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '15',
            'field' => 'number',
            'alias' => 'Number',
            'type' => 'number',
            'length' => '20',
            'decimals' => '0',
            'default' => '0',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'class' => '',
            'query' => '',
            'note' => 'Contoh Type number',
            'position' => '4'
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '16',
            'field' => 'password',
            'alias' => 'Password',
            'type' => 'password',
            'length' => '20',
            'decimals' => '0',
            'default' => 'msjframework',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'class' => '',
            'query' => '',
            'note' => 'Contoh Type password',
            'position' => '4'
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '17',
            'field' => 'search',
            'alias' => 'Search',
            'type' => 'search',
            'length' => '255',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:255|min:3',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'class' => '',
            'query' => "select value, name from sys_enum where idenum = 'layout'",
            'note' => 'Contoh Type search',
            'position' => '4'
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '18',
            'field' => 'string',
            'alias' => 'string',
            'type' => 'string',
            'length' => '20',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:20|min:3',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'class' => '',
            'query' => '',
            'note' => 'Contoh Type string',
            'position' => '4'
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '19',
            'field' => 'text',
            'alias' => 'Text',
            'type' => 'text',
            'length' => '255',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:255|min:3',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'class' => '',
            'query' => '',
            'note' => 'Contoh Type text',
            'position' => '4'
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => '20',
            'field' => 'isactive',
            'alias' => 'Status',
            'type' => 'enum',
            'length' => '1',
            'decimals' => '0',
            'default' => '1',
            'validate' => '',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '0',
            'query' => "select value, name from sys_enum where idenum = 'isactive' and isactive = '1'"
        ]);
    }
}
