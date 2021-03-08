package com.learnjava.service;

import com.learnjava.dao.UserDao;
import com.learnjava.metrics.MetricTime;
import com.learnjava.validator.Validators;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.BeanPropertyRowMapper;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.support.GeneratedKeyHolder;
import org.springframework.jdbc.support.KeyHolder;
import org.springframework.stereotype.Component;
import org.springframework.transaction.PlatformTransactionManager;
import org.springframework.transaction.annotation.Propagation;
import org.springframework.transaction.annotation.Transactional;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;

@Component
@Transactional(propagation = Propagation.REQUIRED)
public class UserService {

	@Autowired
	PlatformTransactionManager txManager;

	@Autowired
	private MailService mailService;

	@Autowired
	Validators validators;

	public UserService(@Autowired MailService mailService) {

		this.mailService = mailService;
	}



	public void setMailService(MailService mailService) {
		this.mailService = mailService;
	}

	private List<User> users = new ArrayList<>(List.of( // users:
			new User(1, "bob@example.com", "password", "Bob"), // bob
			new User(2, "alice@example.com", "password", "Alice"), // alice
			new User(3, "tom@example.com", "password", "Tom"))); // tom

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
		User user = new User(users.stream().mapToLong(u -> u.getId()).max().getAsLong(), email, password, name);
		users.add(user);
		mailService.sendRegistrationMail(user);
		return user;
	}




	@Autowired
	JdbcTemplate jdbcTemplate;

	public User getUserById(long id) {
		return jdbcTemplate.execute((Connection conn) -> {
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


	public User getUserByName(String name) {
		return jdbcTemplate.execute("SELECT * FROM users WHERE name = ?", (PreparedStatement ps) -> {
			ps.setObject(1, name);
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
		});
	}

	public User getUserByEmail(String email) {
		return jdbcTemplate.queryForObject("SELECT * FROM users WHERE email = ?", new Object[] { email },
				(ResultSet rs, int rowNum) -> {
					return new User( // new User object:
							rs.getLong("id"), // id
							rs.getString("email"), // email
							rs.getString("password"), // password
							rs.getString("name")); // name
				});
	}

	public long getUsers() {
		return jdbcTemplate.queryForObject("SELECT COUNT(*) FROM users", null, (ResultSet rs, int rowNum) -> {
			return rs.getLong(1);
		});
	}

	public List<User> getUsers(int pageIndex) {
		int limit = 100;
		int offset = limit * (pageIndex - 1);
		return jdbcTemplate.query("SELECT * FROM users LIMIT ? OFFSET ?", new Object[] { limit, offset },
				new BeanPropertyRowMapper<>(User.class));
	}

	public User login2(String email, String password) {
		User user = getUserByEmail(email);
		if (user.getPassword().equals(password)) {
			return user;
		}
		throw new RuntimeException("login failed.");
	}

	public User register2(String email, String password, String name) {
		KeyHolder holder = new GeneratedKeyHolder();
		if (1 != jdbcTemplate.update((conn) -> {
			var ps = conn.prepareStatement("INSERT INTO users(email,password,name) VALUES(?,?,?)",
					Statement.RETURN_GENERATED_KEYS);
			ps.setObject(1, email);
			ps.setObject(2, password);
			ps.setObject(3, name);
			return ps;
		}, holder)) {
			throw new RuntimeException("Insert failed.");
		}
		return new User(holder.getKey().longValue(), email, password, name);
	}

	public void updateUser(User user) {
		if (1 != jdbcTemplate.update("UPDATE user SET name = ? WHERE id=?", user.getName(), user.getId())) {
			throw new RuntimeException("User not found by id");
		}
	}




	@Autowired
	UserDao userDao;

	public User getUserById2(long id) {
		return userDao.getById(id);
	}

	public User fetchUserByEmail2(String email) {
		return userDao.fetchUserByEmail(email);
	}

	public User getUserByEmail2(String email) {
		return userDao.getUserByEmail(email);
	}



}
