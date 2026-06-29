<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Stringable;

class RoomRecommendationAgent implements Agent, Conversational, HasStructuredOutput, HasTools
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return 'You recommend campus study rooms based on user preferences and constraints. Analyze the list of rooms and return up to 3 recommendations with specific, helpful reasons. Only recommend rooms that are available in the provided rooms list.';
    }

    /**
     * Get the list of messages comprising the conversation so far.
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     */
    public function tools(): iterable
    {
        return [];
    }

    /**
     * Get the agent's structured output schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'summary' => $schema->string()->description('A short summary explaining the recommendations.'),
            'recommendations' => $schema->array()->items(
                $schema->object([
                    'room_id' => $schema->integer()->description('The ID of the recommended room.'),
                    'reason' => $schema->string()->description('A concise reason explaining why this room matches the preference.'),
                ])
            )->description('An array of recommended room objects.'),
        ];
    }
}
