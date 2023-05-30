# 💡CodeRedeem
This plugin is useful for giving server gifts via code</br>
you can set the redeem code as you like in the ```config.yml```</br> </br>

Please Change the Config, Code, and Prizes Without Restarting the Server (Auto Saving)

</br>

# ❗Warning
It is not recommended to change ```data.yml``` or touch any bit!

</br>

# 🗨Command
Command | Description 
--- | ---
`/coderedeem` | Open The CodeRedeem Menu

</br>

# ⚙config.yml
config.yml to set ui and message code redeem
```
Title: "CodeRedeem"
Content: "enter the correct redeem code, and get a prize."

Prize:
  Message-Succes: "Congratulations, You have won the code redeem prize."
  Message-Failed: "Your code is wrong, please try again."
  Message-Claimed: "You have got a Code Redem prize."

Config-Version: "1.0"
```

</br>

# ⚙code.yml
code.yml to set the code and rewards according to your wishes
```
CodeRedeem:
  ABOGOBOGA:
    Reward:
    - "give {player} apple 1"
    - "give {player} bread 1"
  HESOYAM:
    Reward:
    - "give {player} apple 1"
    - "give {player} bread 1"
```
