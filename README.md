# is_rocket_launched
Detect in PHP if rocket is launched

# REQUISITES
- PHP 7.2
- SQLite 3 enable
- A domain/host to Telegram Bot

If need upgrade the code this is the workflow:

Start the code in webhook.php with the function init.
The first of all connect with the api and save this info in a model called Info.
You have 4 commands:
1- /start to start the game/workflow in Telegram
2- /getData to recover the exactly data in database like the ID of the chat, frame obtained, last frame and first frame. This last 3 parameters were updated when user send Yes, No, /start
3- Yes, If user send Yes the last frame is updated with the frame_to_user param
4- No, If user send No the first frame is updated with the frame_to_user param

# SOLUTION:
When the first_frame is mayor than last_frame then return the last frame found it and the "game" end.