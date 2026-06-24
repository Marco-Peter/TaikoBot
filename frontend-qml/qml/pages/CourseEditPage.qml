import QtQuick
import QtQuick.Controls.Material
import QtQuick.Layouts
import "../components"

Page {
    id: root
    property int courseId: 0        // 0 = create new
    property var course: ({})

    header: ToolBar {
        TPageHeader {
            anchors.fill: parent
            title: courseId === 0 ? "New Course" : "Edit Course"
        }
    }

    ScrollView {
        anchors.fill: parent
        contentWidth: availableWidth

        ColumnLayout {
            width: parent.width
            spacing: 16

            TCard {
                Layout.fillWidth: true
                Layout.margins: 12
                height: formCol.implicitHeight + 24

                ColumnLayout {
                    id: formCol
                    anchors { fill: parent; margins: 12 }
                    spacing: 12

                    TextField {
                        id: nameField
                        placeholderText: "Course name *"
                        text: course.name || ""
                        Layout.fillWidth: true
                    }

                    TextArea {
                        id: descField
                        placeholderText: "Description"
                        text: course.description || ""
                        Layout.fillWidth: true
                        wrapMode: TextEdit.Wrap
                        background: Rectangle {
                            radius: 4
                            border.color: Material.dividerColor
                            color: "transparent"
                        }
                    }

                    RowLayout {
                        spacing: 8

                        ColumnLayout {
                            Layout.fillWidth: true
                            Label { text: "Capacity" }
                            SpinBox {
                                id: capacityField
                                from: 1; to: 200
                                value: course.capacity || 20
                                Layout.fillWidth: true
                            }
                        }

                        ColumnLayout {
                            Layout.fillWidth: true
                            Label { text: "Signout limit (h)" }
                            SpinBox {
                                id: signoutField
                                from: 0; to: 168
                                value: course.signout_limit || 24
                                Layout.fillWidth: true
                            }
                        }
                    }

                    RowLayout {
                        spacing: 8

                        ColumnLayout {
                            Layout.fillWidth: true
                            Label { text: "Teacher payment (CHF)" }
                            SpinBox {
                                id: teacherPayField
                                from: 0; to: 9999
                                value: course.teacher_payment || 0
                                Layout.fillWidth: true
                            }
                        }

                        ColumnLayout {
                            Layout.fillWidth: true
                            Label { text: "Assist payment (CHF)" }
                            SpinBox {
                                id: assistPayField
                                from: 0; to: 9999
                                value: course.assist_payment || 0
                                Layout.fillWidth: true
                            }
                        }
                    }

                    // Error
                    Label {
                        visible: CourseStore.error.length > 0
                        text: CourseStore.error
                        color: Material.color(Material.Red)
                        wrapMode: Text.Wrap
                        Layout.fillWidth: true
                    }

                    RowLayout {
                        spacing: 8
                        Layout.fillWidth: true

                        TButton {
                            text: courseId === 0 ? "Create" : "Save"
                            Layout.fillWidth: true
                            enabled: !CourseStore.loading && nameField.text.length > 0
                            onClicked: save()
                        }

                        TButton {
                            text: "Delete"
                            destructive: true
                            visible: courseId !== 0
                            enabled: !CourseStore.loading
                            onClicked: deleteDialog.open()
                        }
                    }

                    BusyIndicator {
                        visible: CourseStore.loading
                        Layout.alignment: Qt.AlignHCenter
                    }
                }
            }

            Item { Layout.preferredHeight: 24 }
        }
    }

    Dialog {
        id: deleteDialog
        title: "Delete this course?"
        modal: true
        anchors.centerIn: Overlay.overlay
        standardButtons: Dialog.Ok | Dialog.Cancel
        onAccepted: {
            CourseStore.deleteCourse(root.courseId)
        }
    }

    Connections {
        target: CourseStore
        function onCurrentCourseChanged() {
            if (!CourseStore.loading && CourseStore.error.length === 0 && root.courseId > 0)
                StackView.view.pop()
        }
        function onCourseDeleted() { StackView.view.pop(); StackView.view.pop() }
    }

    function save() {
        const data = {
            name:            nameField.text,
            description:     descField.text,
            capacity:        capacityField.value,
            signout_limit:   signoutField.value,
            teacher_payment: teacherPayField.value,
            assist_payment:  assistPayField.value,
        }
        if (courseId === 0)
            CourseStore.createCourse(data)
        else
            CourseStore.updateCourse(courseId, data)
    }
}
