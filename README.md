The task is to make a simple application that can take
commands, such as "Write to DB in 15 minutes"
and it will execute said command at exactly that time.

The commands can be received via CLI or via some frontend.
And the schedule can run via cron or via supervisor or some
other utility.

Example:
cli task:add +15m "Hello World"
cli task:add +3m "Hello 23e3f2World"

Task will be assessed based on the overall architecture of the code, including but not limited to: folder structure, usage of PSR standards, design patterns, overall code cleanliness and general knowledge of PHP.

