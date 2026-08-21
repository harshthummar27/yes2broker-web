<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AmenityOptionResource\Pages;
use App\Models\AmenityOption;
use App\Support\AmenityIcon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AmenityOptionResource extends Resource
{
    protected static ?string $model = AmenityOption::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Amenities';

    protected static ?string $modelLabel = 'Amenity';

    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        $iconOptions = collect(AmenityIcon::availableIcons())
            ->mapWithKeys(fn (string $icon) => [$icon => Str::headline(str_replace('-', ' ', $icon))])
            ->all();

        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state, Forms\Get $get): void {
                                if (blank($get('slug'))) {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('icon')
                            ->options($iconOptions)
                            ->required()
                            ->default('default')
                            ->native(false),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('icon')
                    ->badge()
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Amenity deleted')
                            ->body('The amenity option was successfully deleted.')
                    )
                    ->failureNotification(
                        Notification::make()
                            ->danger()
                            ->title('Failed to delete amenity')
                            ->body('An error occurred while deleting the amenity.')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Amenities deleted')
                                ->body('The selected amenities were successfully deleted.')
                        )
                        ->failureNotification(
                            Notification::make()
                                ->danger()
                                ->title('Failed to delete amenities')
                                ->body('Some or all selected amenities could not be deleted.')
                        ),
                ]),
            ])
            ->emptyStateHeading('No amenities found')
            ->emptyStateDescription('Create amenity options (e.g. Swimming Pool, Gym, Club House, Power Backup).')
            ->emptyStateIcon('heroicon-o-sparkles')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Amenity')
                    ->icon('heroicon-m-plus'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAmenityOptions::route('/'),
            'create' => Pages\CreateAmenityOption::route('/create'),
            'edit' => Pages\EditAmenityOption::route('/{record}/edit'),
        ];
    }
}
