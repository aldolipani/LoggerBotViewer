package loggerbotviewer;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

@RestController
public class LogController {

    @RequestMapping(value = "/userlist")
    private List<User> user() {
        List<User> userlist = new ArrayList();
        Connection c = null;
        Statement stmt = null;
        try {
            Class.forName("org.sqlite.JDBC");
            c = DriverManager.getConnection("jdbc:sqlite:test.db");
            stmt = c.createStatement();
            ResultSet rs = stmt.executeQuery("SELECT * FROM user");
            while (rs.next()) {
                String email = rs.getString("email");
                userlist.add(new User(email));
            }
            rs.close();
            stmt.close();
            c.close();
        } catch (Exception e) {
            System.err.println(e.getClass().getName() + ": " + e.getMessage());
            System.exit(0);
        }

        return userlist;
    }

    @RequestMapping("/log")
    private List<UserLog> userlog(@RequestParam(value = "user", defaultValue = "all") String user) {
        List<UserLog> userlog = new ArrayList();
        Connection c = null;
        Statement stmt = null;
        try {
            Class.forName("org.sqlite.JDBC");
            c = DriverManager.getConnection("jdbc:sqlite:test.db");
            stmt = c.createStatement();
            if (user.equals("all")) {
                ResultSet rs = stmt.executeQuery("SELECT U.email, L.id_user, L.content, strftime(\"%d-%m-%Y %H:%M\", L.date, 'unixepoch') FROM log AS L, user AS U WHERE U.id = L.id_user");
                while (rs.next()) {
                    String email = rs.getString("email");
                    String content = rs.getString("content");
                    String date = rs.getString(4);
                    userlog.add(new UserLog(email, content, date));
                }
                rs.close();
            } else {
                ResultSet rs = stmt.executeQuery("SELECT id FROM user WHERE email = '".concat(user).concat("'"));
                String userid = "";
                while (rs.next()) {
                    userid = rs.getString("id");
                }
                rs = stmt.executeQuery("SELECT content, strftime(\"%d-%m-%Y %H:%M\", date, 'unixepoch') FROM log WHERE id_user = '".concat(userid).concat("'"));
                while (rs.next()) {
                    String content = rs.getString("content");
                    String date = rs.getString(2);
                    userlog.add(new UserLog(user, content, date));
                }
                rs.close();
            }
            stmt.close();
            c.close();
        } catch (Exception e) {
            System.err.println(e.getClass().getName() + ": " + e.getMessage());
            System.exit(0);
        }

        return userlog;
    }
}
