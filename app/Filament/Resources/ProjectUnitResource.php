<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectUnitResource\Pages;
use App\Models\ProjectUnit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProjectUnitResource extends Resource
{
    protected static ?string $model = ProjectUnit::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $navigationLabel = 'Project Units';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Unit name')
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
                    ->label('Unit')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                            ->title('Project unit deleted')
                            ->body('The project unit was successfully deleted.')
                    )
                    ->failureNotification(
                        Notification::make()
                            ->danger()
                            ->title('Failed to delete project unit')
                            ->body('An error occurred while deleting the project unit.')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Project units deleted')
                                ->body('The selected project units were successfully deleted.')
                        )
                        ->failureNotification(
                            Notification::make()
                                ->danger()
                                ->title('Failed to delete project units')
                                ->body('Some or all selected project units could not be deleted.')
                        ),
                ]),
            ])
            ->emptyStateHeading('No project units found')
            ->emptyStateDescription('Create project units like Sq. Ft., Sq. Yards, Acres, etc.')
            ->emptyStateIcon('heroicon-o-square-3-stack-3d')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Project Unit')
                    ->icon('heroicon-m-plus'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectUnits::route('/'),
            'create' => Pages\CreateProjectUnit::route('/create'),
            'edit' => Pages\EditProjectUnit::route('/{record}/edit'),
        ];
    }
}
