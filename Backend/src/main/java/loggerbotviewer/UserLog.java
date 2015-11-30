package loggerbotviewer;

public class UserLog {

    private String user;
    private final String content;
    private final String date;

    public UserLog(String content, String date) {
        this.content = content;
        this.date = date;
    }

    public UserLog(String user, String content, String date) {
        this.user = user;
        this.content = content;
        this.date = date;
    }

    public String getContent() {
        return content;
    }

    public String getDate() {
        return date;
    }

    public String getUser() {
        return user;
    }

    public void setUser(String user) {
        this.user = user;
    }
    
    

}
