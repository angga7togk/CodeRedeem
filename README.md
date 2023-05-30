# CodeRedeem
This plugin is useful for giving server gifts via code</br>
you can set the redeem code as you like in the config.yml

# 💡Important Tips
After changing the code or reward in the config.yml, try to delete the data.json, so that the coderedeem you just changed can be used,

No Need to Restart Server After Changing Config

# 🗨Command
Command | Description 
--- | ---
`/coderedeem` | Open The CodeRedeem Menu

# ⚙config 
```
Title: "CodeRedeem"
Content: "enter the correct redeem code, and get a prize."

Reward:
  - "give {player} apple 1"
  - "give {player} bread 1"

Prize:
  Code: "ABOGOBOGA"
  Message-Succes: "Congratulations, {player} have won the code redeem prize."
  Message-Failed: "Your code is wrong, please try again."
  Message-Claimed: "You have got a Code Redem prize."

Config-Version: "1.0"

```
