# Cloze Questions Helpers

A simple, client-side web utility meant to assist teachers and administrators working with **Moodle Cloze** questions. 

## What was this intended for?

In Moodle, "Short Answer" type questions can be notoriously strict when it comes to exact string matching. This causes a frequent problem when grading answers containing apostrophes because students might input different typographical variations of the same character depending on their device or keyboard layout:
- The standard straight coat (`'`)
- The typographic right single curly quote (`’`) 
- The typographic left single curly quote (`‘`)

If a teacher only provides the straight quote as the correct answer, a student using a mobile device that defaults to curly quotes might be wrongfully graded as incorrect.

This small tool solves that problem automatically. It takes a base answer string with an apostrophe and automatically transforms it into a Moodle-compliant Cloze string containing all common apostrophe variations as acceptable matches.

### Example
**Input:** `It's`

**Output:** `{1:SHORTANSWER:=It's~=It’s~=It‘s}`

## Author
Created by [@kbtale](https://github.com/kbtale)

*Note: This project consists of a single generic HTML file and and is currently archived. I do recognize this was used by some people a long time ago, but it's no longer maintained.*
