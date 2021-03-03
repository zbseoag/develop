package com.service;

import com.annotation.MetricTime;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.support.GeneratedKeyHolder;
import org.springframework.jdbc.support.KeyHolder;
import org.springframework.stereotype.Component;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;

@Component
public class UserService {

	@Autowired
	private MailService mailService;

	@Autowired
	JdbcTemplate jdbcTemplate;


	private List<User> users = new ArrayList<>(List.of(
		new User(1, "bob@example.com", "password", "Bob"),
		new User(2, "alice@example.com", "password", "Alice")
	));

	public User login(String email, String password) {

		for (User user : users) {
			if (user.getEmail().equalsIgnoreCase(email) && user.getPassword().equals(password)) {

				mailService.sendLoginMail(user);
				return user;
			}
		}

		throw new RuntimeException("login failed.");

	}

	public User getUser(long id) {
		return this.users.stream().filter(user -> user.getId() == id).findFirst().orElseThrow();
	}

	@MetricTime("register")
	public User register(String email, String password, String name) {

		users.forEach((user) -> {
			if (user.getEmail().equalsIgnoreCase(email)) {
				throw new RuntimeException("email exist.");
			}
		});

		long id = users.stream().mapToLong(u -> u.getId()).max().getAsLong();
		User user = new User(id, email, password, name);
		users.add(user);

		mailService.sendRegistrationMail(user);

		return user;

	}

	public User register2(String email, String password, String name) {

		KeyHolder holder = new GeneratedKeyHolder();

		if (jdbcTemplate.update((conn) -> {
			var ps = conn.prepareStatement("INSERT INTO users(`email`,`password`,`name`) VALUES(?,?,?)", Statement.RETURN_GENERATED_KEYS);
			ps.setObject(1, email);
			ps.setObject(2, password);
			ps.setObject(3, name);
			return ps;

		}, holder) != 1) {
			throw new RuntimeException("注册失败！");
		}

		return new User(holder.getKey().longValue(), email, password, name);
	}

	public User getUserByName(String name) {
		return jdbcTemplate.execute("SELECT * FROM users WHERE name = ?", (PreparedStatement ps) -> {
			ps.setObject(1, name);
			try (var rs = ps.executeQuery()) {
				if (rs.next()) {
					return new User(rs.getLong("id"), rs.getString("email"), rs.getString("password"), rs.getString("name"));
				}
				throw new RuntimeException("user not found by id.");
			}
		});
	}



	public User getUserById(long id) {
		// 注意传入的是ConnectionCallback:

		return jdbcTemplate.execute((Connection conn) -> {
			// 可以直接使用conn实例，不要释放它，回调结束后JdbcTemplate自动释放:
			// 在内部手动创建的PreparedStatement、ResultSet必须用try(...)释放:
			try (var ps = conn.prepareStatement("SELECT * FROM users WHERE id = ?")) {
				ps.setObject(1, id);
				try (var rs = ps.executeQuery()) {
					if (rs.next()) {
						return new User( // new User object:
								rs.getLong("id"), // id
								rs.getString("email"), // email
								rs.getString("password"), // password
								rs.getString("name")); // name
					}
					throw new RuntimeException("user not found by id.");
				}
			}
		});
	}

}
