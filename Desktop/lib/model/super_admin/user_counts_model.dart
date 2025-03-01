class UserCounts {
  final int members;
  final int instructors;

  UserCounts({required this.members, required this.instructors});

  factory UserCounts.fromJson(Map<String, dynamic> json) {
    return UserCounts(
      members: json["members"] ?? 0,
      instructors: json["instructors"] ?? 0,
    );
  }
}
