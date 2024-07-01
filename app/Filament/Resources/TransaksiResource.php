<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiResource\Pages;
use App\Filament\Resources\TransaksiResource\RelationManagers;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Http\Request;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransaksiResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Transaksi::class;
    protected static ?string $modelLabel = 'Transaksi Penjualan';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Select::make('obat_id')
                    ->relationship('obat', 'nama_obat')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(auth()->user()->id)
                    ->disabled()
                    ->required(),
                Forms\Components\DatePicker::make('tanggal')
                    ->required(),
                Forms\Components\TextInput::make('jumlah')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('total_harga')
                    ->disabled()
                    ->visible(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('obat.nama_obat')
                    ->label('Nama Obat')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->getStateUsing(function ($record) {
                        return Carbon::parse($record->tanggal)->format('m/d/y');
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pegawai')
                    ->badge()
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Pegawai')
                    ->options(function () {
                        return \App\Models\User::pluck('name', 'id')->toArray();
                    }),
                Tables\Filters\SelectFilter::make('obat_id')
                    ->label('Obat')
                    ->options(function () {
                        return \App\Models\Obat::pluck('nama_obat', 'id')->toArray();
                    }),
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = Indicator::make('Dibuat dari ' . Carbon::parse($data['created_from'])->toFormattedDateString())
                                ->removeField('created_from');
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = Indicator::make('Dibuat sampai ' . Carbon::parse($data['created_until'])->toFormattedDateString())
                                ->removeField('created_until');
                        }

                        return $indicators;
                    })
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTransaksis::route('/'),
            'create' => Pages\CreateTransaksi::route('/create'),
            // 'edit' => Pages\EditTransaksi::route('/{record}/edit'),
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

    public function exportToPdf(Request $request)
    {
        $user_id = $request->user_id;
        $obat_id = $request->obat_id;
        $created_from = $request->created_from;
        $created_until = $request->created_until;

        $query = Transaksi::query();

        if ($user_id) {
            $query->where('user_id', $user_id);
        }

        if ($obat_id) {
            $query->where('obat_id', $obat_id);
        }

        if ($created_from) {
            $query->whereDate('tanggal', '>=', $created_from);
        }

        if ($created_until) {
            $query->whereDate('tanggal', '<=', $created_until);
        }

        if ($request->has('sort')) {
            $sortField = $request->input('sort');
            $sortDirection = $request->input('direction', 'asc');

            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('tanggal', 'asc');
        }

        $transaksis = $query->get();

        $pdf = FacadePdf::loadView('transaksi.pdf', compact('transaksis'));

        return $pdf->download('transaksi.pdf');
    }
}
