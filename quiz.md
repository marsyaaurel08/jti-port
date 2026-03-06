# Quiz JSON Examples

This document provides JSON examples for each question type.


## 1. Single Choice

```json
// question_text
"What is the capital of France?"

// question_config
{
  "media": {
    "type": "image",
    "media_id": 123,
    "alt_text": "Map of France"
  },
  "options": [
    { "id": "A", "text": "London" },
    { "id": "B", "text": "Paris" },
    { "id": "C", "text": "Berlin" },
    { "id": "D", "text": "Madrid" }
  ],
  "shuffle_options": true
}

// answer_key
{
  "correct": "B"
}
```

## 2. Multiple Choice

```json
// question_text
"Which of the following are programming languages?"

// question_config
{
  "options": [
    { "id": "A", "text": "Python", "points": 1 },
    { "id": "B", "text": "JavaScript", "points": 1 },
    { "id": "C", "text": "HTML", "points": 0 },
    { "id": "D", "text": "Java", "points": 1 }
  ],
  "min_correct": 2,
  "max_correct": 3,
  "partial_scoring": true,
  "shuffle_options": true
}

// answer_key
{
  "correct": ["A", "B", "D"],
  "scoring": "partial"
}
```

## 3. Boolean

```json
// question_text
"The Earth is flat."

// question_config
{
  "media": null
}

// answer_key
{
  "correct": false
}
```

## 4. Essay

```json
// question_text
"Explain the importance of biodiversity in ecosystems."

// question_config
{
  "media": {
    "type": "image",
    "media_id": 456,
    "alt_text": "Diverse ecosystem"
  },
  "max_words": 500,
  "min_words": 100,
  "grading_criteria": [
    "Content accuracy",
    "Scientific terminology",
    "Logical structure"
  ]
}

// answer_key
{
  "manual_grading": true
}
```

## 5. Matching

```json
// question_text
"Match each country with its capital city."

// question_config
{
  "items": [
    { "id": "1", "text": "Japan" },
    { "id": "2", "text": "Brazil" },
    { "id": "3", "text": "Australia" }
  ],
  "matches": [
    { "id": "A", "text": "Tokyo" },
    { "id": "B", "text": "Brasília" },
    { "id": "C", "text": "Canberra" }
  ],
  "shuffle_items": true,
  "shuffle_matches": true
}

// answer_key
{
  "correct_matches": {
    "1": "A",
    "2": "B",
    "3": "C"
  }
}
```

## 6. Drag Drop Sorting

```json
// question_text
"Arrange the steps of photosynthesis in the correct order."

// question_config
{
  "media": {
    "type": "image",
    "media_id": 789,
  },
  "items": [
    { "id": "step1", "text": "Light absorption by chlorophyll" },
    { "id": "step2", "text": "Water splitting (photolysis)" },
    { "id": "step3", "text": "Electron transport chain" },
    { "id": "step4", "text": "ATP and NADPH production" }
  ],
  "shuffle_items": true,
  "allow_partial_credit": true
}

// answer_key
{
  "correct_order": ["step1", "step2", "step3", "step4"],
  "scoring": "partial"
}
```

<!-- ## 7. Drag Drop Zones

```json
// question_text
"Drag each organ to its correct body system."

// question_config
{
  "zones": [
    { "id": "circulatory", "label": "Circulatory System" },
    { "id": "respiratory", "label": "Respiratory System" },
    { "id": "digestive", "label": "Digestive System" }
  ],
  "items": [
    { "id": "heart", "text": "Heart" },
    { "id": "lungs", "text": "Lungs" },
    { "id": "stomach", "text": "Stomach" }
  ],
  "shuffle_items": true
}

// answer_key
{
  "correct_placements": {
    "heart": "circulatory",
    "lungs": "respiratory",
    "stomach": "digestive"
  }
}
``` -->

<!-- ## 8. Fill in Blank

```json
// question_text
"Complete the following sentences about the water cycle."

// question_config
{
  "template": "The process of water changing from liquid to gas is called {evaporation}. When water vapor cools and forms clouds, this is called {condensation}.",
  "blanks": [
    { "id": "evaporation", "case_sensitive": false },
    { "id": "condensation", "case_sensitive": false }
  ]
}

// answer_key
{
  "correct_answers": {
    "evaporation": ["evaporation", "Evaporation"],
    "condensation": ["condensation", "Condensation"]
  }
}
``` -->

## User Answer Examples

```json
// single_choice
{ "selected": "B" }

// multiple_choice
{ "selected": ["A", "B", "D"] }

// boolean
{ "selected": false }

// essay
{ "text": "Biodiversity is crucial for ecosystem stability..." }

// matching
{ "matches": { "1": "A", "2": "B", "3": "C" } }

// drag_drop_sorting
{ "order": ["step1", "step2", "step3", "step4"] }

// drag_drop_zones
{ "placements": { "heart": "circulatory", "lungs": "respiratory" } }

// fill_in_blank
{ "answers": { "evaporation": "evaporation", "condensation": "condensation" } }
```
