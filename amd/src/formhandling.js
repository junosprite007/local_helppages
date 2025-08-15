// This file is part of FLIP Plugins for Moodle
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Form handling enhancements for help pages.
 *
 * @module     local_helppages/formhandling
 * @copyright  2025 Fun Learning Company
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export const init = () => {
  // Auto-generate page name from title
  const titleInput = document.getElementById("id_title");
  const nameInput = document.getElementById("id_name");

  if (titleInput && nameInput) {
    titleInput.addEventListener("input", () => {
      // Only auto-generate if name field is empty
      if (!nameInput.value.trim()) {
        const title = titleInput.value;
        const name = generatePageName(title);
        nameInput.value = name;
      }
    });
  }

  // Add confirmation for delete actions using Moodle's modal system
  const deleteLinks = document.querySelectorAll('a[href*="action=delete"]');
  deleteLinks.forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault();

      // Use Moodle's modal confirmation system
      require([
        "core/modal_factory",
        "core/modal_events",
        "core/str",
      ], function (ModalFactory, ModalEvents, str) {
        str
          .get_strings([
            { key: "delete", component: "local_helppages" },
            { key: "confirmdelete", component: "local_helppages" },
            { key: "delete", component: "moodle" },
            { key: "cancel", component: "moodle" },
          ])
          .then(function (strings) {
            return ModalFactory.create({
              type: ModalFactory.types.SAVE_CANCEL,
              title: strings[0],
              body: strings[1],
              buttons: {
                save: strings[2],
                cancel: strings[3],
              },
            }).then(function (modal) {
              modal.setSaveButtonText(strings[2]);
              modal.getRoot().on(ModalEvents.save, function () {
                // Redirect to delete URL
                window.location.href = link.href;
              });
              modal.show();
              return modal;
            });
          })
          .catch(function () {
            // Fallback to basic confirm if modal fails
            if (
              confirm(
                link.dataset.confirmMessage ||
                  "Are you sure you want to delete this page?"
              )
            ) {
              window.location.href = link.href;
            }
          });
      });
    });
  });

  // Form validation enhancements
  const pageForm = document.querySelector('form[data-form="page"]');
  if (pageForm) {
    pageForm.addEventListener("submit", (e) => {
      const titleField = pageForm.querySelector("#id_title");
      const nameField = pageForm.querySelector("#id_name");

      if (titleField && !titleField.value.trim()) {
        e.preventDefault();
        titleField.focus();
        showError("Title is required");
        return;
      }

      if (nameField && nameField.value && !isValidPageName(nameField.value)) {
        e.preventDefault();
        nameField.focus();
        showError(
          "Page name can only contain letters, numbers, hyphens and underscores"
        );
        return;
      }
    });
  }
};

/**
 * Generate a URL-safe page name from a title
 * @param {string} title - The page title
 * @returns {string} URL-safe page name
 */
const generatePageName = (title) => {
  return title
    .toLowerCase()
    .replace(/[^a-z0-9\s-_]/g, "") // Remove special characters
    .replace(/\s+/g, "-") // Replace spaces with hyphens
    .replace(/-+/g, "-") // Replace multiple hyphens with single
    .replace(/^-+|-+$/g, ""); // Remove leading/trailing hyphens
};

/**
 * Validate page name format
 * @param {string} name - The page name to validate
 * @returns {boolean} Whether the name is valid
 */
const isValidPageName = (name) => {
  return /^[a-zA-Z0-9_-]+$/.test(name);
};

/**
 * Show error message to user
 * @param {string} message - Error message to display
 */
const showError = (message) => {
  // Use Moodle's notification system if available
  if (window.require) {
    require(["core/notification"], function (notification) {
      notification.addNotification({
        message: message,
        type: "error",
      });
    });
  } else {
    // Fallback to alert
    alert(message);
  }
};
