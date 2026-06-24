import QtQuick
import QtQuick.Controls.Material
import QtQuick.Layouts
import "../components"

Page {
    id: root
    property int courseId: 0

    header: ToolBar {
        RowLayout {
            anchors.fill: parent
            anchors.leftMargin: 4
            anchors.rightMargin: 8

            TPageHeader {
                Layout.fillWidth: true
                title: CourseStore.currentCourse.name || "Course"
            }

            ToolButton {
                visible: AuthStore.canEditCourses
                text: "Edit"
                onClicked: StackView.view.push("CourseEditPage.qml",
                               { courseId: root.courseId,
                                 course: CourseStore.currentCourse })
            }
        }
    }

    ScrollView {
        anchors.fill: parent
        contentWidth: availableWidth

        ColumnLayout {
            width: parent.width
            spacing: 8

            // Info card
            TCard {
                Layout.fillWidth: true
                Layout.margins: 12
                height: infoCol.implicitHeight + 24

                ColumnLayout {
                    id: infoCol
                    anchors { fill: parent; margins: 12 }
                    spacing: 6

                    Label {
                        text: CourseStore.currentCourse.description || ""
                        wrapMode: Text.Wrap
                        Layout.fillWidth: true
                        visible: (CourseStore.currentCourse.description || "") !== ""
                    }

                    RowLayout {
                        Label { text: "Capacity:"; opacity: 0.7 }
                        Label {
                            text: (CourseStore.currentCourse.participants_count || 0)
                                  + " / " + (CourseStore.currentCourse.capacity || "?")
                            font.bold: true
                        }
                    }

                    RowLayout {
                        Label { text: "Signout limit:"; opacity: 0.7 }
                        Label {
                            text: (CourseStore.currentCourse.signout_limit || 0) + " h"
                            font.bold: true
                        }
                    }
                }
            }

            // Lessons header + add button
            RowLayout {
                Layout.leftMargin: 12
                Layout.rightMargin: 12

                Label {
                    text: "Lessons"
                    font.bold: true
                    font.pixelSize: 14
                    color: Material.accent
                    Layout.fillWidth: true
                }

                TButton {
                    visible: AuthStore.canEditCourses
                    text: "+ Add lesson"
                    flat: true
                    onClicked: StackView.view.push("LessonEditPage.qml",
                                   { lessonId: 0, courseId: root.courseId })
                }
            }

            BusyIndicator {
                visible: CourseStore.loading
                Layout.alignment: Qt.AlignHCenter
            }

            // Lesson list from course.lessons
            Repeater {
                model: courseLessons

                TCard {
                    required property var modelData
                    required property int index
                    Layout.fillWidth: true
                    Layout.leftMargin: 12
                    Layout.rightMargin: 12
                    height: lessonRow.implicitHeight + 16

                    MouseArea {
                        anchors.fill: parent
                        onClicked: StackView.view.push("LessonShowPage.qml",
                                       { lessonId: modelData.id })
                    }

                    RowLayout {
                        id: lessonRow
                        anchors { fill: parent; margins: 8 }

                        ColumnLayout {
                            spacing: 2
                            Layout.fillWidth: true

                            Label {
                                text: modelData.title ||
                                      Qt.formatDateTime(new Date(modelData.start), "ddd dd.MM.yyyy HH:mm")
                                font.bold: true
                                elide: Text.ElideRight
                                Layout.fillWidth: true
                            }
                            Label {
                                text: Qt.formatDateTime(new Date(modelData.start), "HH:mm") + " – " +
                                      Qt.formatDateTime(new Date(modelData.finish), "HH:mm")
                                visible: modelData.title && modelData.title.length > 0
                                font.pixelSize: 12
                                opacity: 0.7
                            }
                        }

                        Label {
                            text: (modelData.participants_count || 0) + " participants"
                            font.pixelSize: 12
                            opacity: 0.7
                        }
                    }
                }
            }

            Label {
                visible: courseLessons.length === 0 && !CourseStore.loading
                text: "No lessons scheduled"
                opacity: 0.5
                Layout.alignment: Qt.AlignHCenter
                Layout.topMargin: 16
            }

            // Signup button for students
            TButton {
                visible: !AuthStore.canEditCourses && !isSignedUp
                text: "Sign up for this course"
                Layout.alignment: Qt.AlignHCenter
                Layout.topMargin: 8
                onClicked: CourseStore.signup(root.courseId)
            }

            Item { Layout.preferredHeight: 24 }
        }
    }

    property var courseLessons: {
        const lessons = CourseStore.currentCourse.lessons
        if (!lessons) return []
        if (Array.isArray(lessons)) return lessons
        if (lessons.data) return lessons.data
        return []
    }

    property bool isSignedUp: false  // derived from participant list; simplification

    Component.onCompleted: CourseStore.loadCourse(courseId)

    Connections {
        target: CourseStore
        function onCourseUpdated(id) { if (id === root.courseId) CourseStore.loadCourse(id) }
    }
}
