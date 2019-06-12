# hakan-plugin 
## **v0.0.1**
---
Just another **Wordpress plugin** developed by **Hakan BİŞGİN**.
Developed for like button and functionality. 
Includes ==Top 10 liked post== widget and ==Top Liked Tags== panel.

::: warning
At this document **'wp_'** is used instead of the **db_prefix**.
:::
*[AJAX]: Asynchronous JavaScript and XML

This plugin: 
+ migrates =='wp_post_likes'== table for like records.
+ uses **AJAX** for the =='like_button_clicked'== function.
---
## **To use**

>Copy ==hakan-plugin== folder to ==...wp-content\plugins== location under the wordpress location.
Then activate the plugin under **installed plugin** table.

---
There are two user identification methods implemented for the separation of liked and disliked posts. 

The method can be changed with the  =='hakan_method_type'== value at =='wp_options'== table .


---

| Method| Descriptions | hakan_method_type |
| --- | --- | --- |
| use login | **(default)** use ==user_id== value (if the user not logged in, the like button is not avaliable for user)|must be set with "0" |
| use cookie| use cookie (create cookie if cookie not set)| must be set with "1" |

---
:::warning
### **if you want to communicate with me**
[github /hakanbisgin](https://github.com/hakanbisgin)  
[linked.in](https://www.linkedin.com/in/hakan-bişgin-184368138/)  
[f /hakan256](https://www.facebook.com/hakan256)  
[t /syperoflife](https://twitter.com/syperoflife)  
[ig /hakan256](https://www.instagram.com/hakan256/)  
