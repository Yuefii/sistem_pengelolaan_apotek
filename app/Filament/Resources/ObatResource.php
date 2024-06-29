<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObatResource\Pages;
use App\Filament\Resources\ObatResource\RelationManagers;
use App\Models\Obat;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class ObatResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Obat::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Kelola Obat';
    protected static ?string $navigationGroup = 'Management System';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\TextInput::make('kode_obat')->required()->unique(ignorable: fn ($record) => $record),
                Forms\Components\TextInput::make('nama_obat')->required(),
                Forms\Components\TextInput::make('dosis_obat')->required(),
                Forms\Components\TextInput::make('harga_obat')->required(),
                Forms\Components\TextInput::make('kemasan_obat')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('kode_obat')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_obat')
                    ->description(fn (Obat $record): string => $record->dosis_obat)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('harga_obat')
                    ->description(fn (Obat $record): string => $record->kemasan_obat)
                    ->numeric()
                    ->money('IDR', locale: 'id')
                    ->formatStateUsing(fn ($state): string => 'Rp ' . number_format($state, 2, ',', '.'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_stok_obat')
                    ->label('Total Stok')
                    ->color('gray')
                    ->badge()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-horizontal'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListObats::route('/'),
            'create' => Pages\CreateObat::route('/create'),
            'edit' => Pages\EditObat::route('/{record}/edit'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any'
        ];
    }
}
