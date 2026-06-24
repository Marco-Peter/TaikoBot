import QtQuick
import QtQuick.Controls

// Central StackView-based navigator
StackView {
    id: stack

    property string currentTitle: ""

    function navigate(route, params) {
        params = params || {}
        switch (route) {
        case "dashboard":
            currentTitle = "Dashboard"
            stack.replace(null, "qrc:/TaikoBot/qml/pages/DashboardPage.qml", params)
            break
        case "courses":
            currentTitle = "Courses"
            stack.push("qrc:/TaikoBot/qml/pages/CourseIndexPage.qml", params)
            break
        case "course_show":
            currentTitle = params.courseName || "Course"
            stack.push("qrc:/TaikoBot/qml/pages/CourseShowPage.qml", params)
            break
        case "course_edit":
            currentTitle = "Edit Course"
            stack.push("qrc:/TaikoBot/qml/pages/CourseEditPage.qml", params)
            break
        case "lesson_show":
            currentTitle = params.lessonTitle || "Lesson"
            stack.push("qrc:/TaikoBot/qml/pages/LessonShowPage.qml", params)
            break
        case "lesson_edit":
            currentTitle = "Edit Lesson"
            stack.push("qrc:/TaikoBot/qml/pages/LessonEditPage.qml", params)
            break
        case "users":
            currentTitle = "Users"
            stack.push("qrc:/TaikoBot/qml/pages/UserIndexPage.qml", params)
            break
        case "user_edit":
            currentTitle = "Edit User"
            stack.push("qrc:/TaikoBot/qml/pages/UserEditPage.qml", params)
            break
        case "profile":
            currentTitle = "Profile"
            stack.push("qrc:/TaikoBot/qml/pages/ProfilePage.qml", params)
            break
        default:
            console.warn("Unknown route:", route)
        }
    }

    // Start on dashboard
    Component.onCompleted: navigate("dashboard")

    // Back button support on Android / browser history
    Keys.onBackPressed: {
        if (stack.depth > 1) {
            stack.pop()
            event.accepted = true
        }
    }
}
