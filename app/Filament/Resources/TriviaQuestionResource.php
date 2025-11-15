<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TriviaQuestionResource\Pages;
use App\Filament\Resources\TriviaQuestionResource\RelationManagers;
use App\Models\TriviaQuestion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TriviaQuestionResource extends Resource
{
    protected static ?string $model = TriviaQuestion::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Store Management';

    protected static ?int $navigationSort = 10;

    // Only staff members can access this resource
    protected static ?string $permission = 'settings:core';

    public static function getNavigationLabel(): string
    {
        return 'Trivia Questions';
    }

    public static function getPluralLabel(): string
    {
        return 'Trivia Questions';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Question Details')
                    ->schema([
                        Forms\Components\Textarea::make('question')
                            ->required()
                            ->maxLength(500)
                            ->rows(3)
                            ->label('Question')
                            ->helperText('Enter the Bible trivia question'),
                        
                        Forms\Components\TextInput::make('correct_answer')
                            ->required()
                            ->maxLength(255)
                            ->label('Correct Answer'),
                        
                        Forms\Components\Repeater::make('wrong_answers')
                            ->schema([
                                Forms\Components\TextInput::make('answer')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Wrong Answer'),
                            ])
                            ->label('Wrong Answers')
                            ->helperText('Add 3 wrong answers (these will be shuffled with the correct answer)')
                            ->minItems(3)
                            ->maxItems(3)
                            ->defaultItems(3)
                            ->columnSpanFull()
                            ->mutateDehydratedStateUsing(function ($state) {
                                return array_map(fn($item) => $item['answer'], $state);
                            })
                            ->mutateRelationshipDataBeforeFillUsing(function ($data, $state) {
                                return array_map(fn($answer) => ['answer' => $answer], $state ?? []);
                            }),
                        
                        Forms\Components\Toggle::make('active')
                            ->label('Active')
                            ->helperText('Only active questions will be shown to users')
                            ->default(true),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('correct_answer')
                    ->searchable()
                    ->label('Correct Answer'),
                
                Tables\Columns\IconColumn::make('active')
                    ->boolean()
                    ->label('Active'),
                
                Tables\Columns\TextColumn::make('attempts_count')
                    ->counts('attempts')
                    ->label('Total Attempts'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Active Status')
                    ->placeholder('All Questions')
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn ($records) => $records->each->update(['active' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListTriviaQuestions::route('/'),
            'create' => Pages\CreateTriviaQuestion::route('/create'),
            'edit' => Pages\EditTriviaQuestion::route('/{record}/edit'),
        ];
    }
}
